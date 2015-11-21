<?php

add_action("wp_ajax_nopriv_thrive_generate_related_posts", "_thrive_generate_related_posts");
add_action("wp_ajax_thrive_generate_related_posts", "_thrive_generate_related_posts");
add_action("save_post", 'thrive_update_related_posts_hook');

function thrive_update_related_posts_hook($post_id) {

    if (wp_is_post_revision($post_id))
        return;

    $relatedPosts = _thrive_get_related_posts($post_id);
    _thrive_update_related_posts($post_id, $relatedPosts);
}

function _thrive_generate_related_posts() {

    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_generate_related_posts")) {
        echo 0;
        die;
    }

    $posts = get_posts();

    foreach ($posts as $post) {
        $relatedPosts = _thrive_get_related_posts($post->ID);
        echo $post->ID . " - " . $relatedPosts . "\n";
        _thrive_update_related_posts($post->ID, $relatedPosts);
    }
    echo 1;
    die;
}

function _thrive_update_related_posts($postId, $relatedPosts) {

    add_post_meta($postId, '_thrive_meta_related_posts_list', $relatedPosts, true) or
            update_post_meta($postId, '_thrive_meta_related_posts_list', $relatedPosts);
}

function _thrive_get_related_posts($postId, $method = 'json', $post_number = 10) {

    $ignoreCats = json_decode(thrive_get_theme_options("related_ignore_cats"));
    $ignoreTags = json_decode(thrive_get_theme_options("related_ignore_tags"));
    $noPosts = thrive_get_theme_options("related_number_posts");

    if (!is_array($ignoreCats)) {
        $ignoreCats = array();
    }
    if (!is_array($ignoreTags)) {
        $ignoreTags = array();
    }

    $relatedPostsJSON = array();
    $relatedPostsArray = array();

    $current_post = get_post($postId);
    //replace - with empty space so we can get more words
    $title_words = explode(' ', str_replace('-', ' ', $current_post->post_title));
    $all_posts = get_posts(array('posts_per_page' => -1, 'category__not_in' => $ignoreCats, 'tag__not_in' => $ignoreTags));

    $_MATCH_THRESHOLD = 1;
    global $excluded_words;

    foreach ($title_words as $key => $word) {
        if (in_array($word, $excluded_words)) {
            unset($title_words[$key]);
        }
    }

    foreach ($all_posts as $p) {
        if ($p->ID == $postId)
            continue;
        $words_match = 0;
        foreach ($title_words as $word) {
            if (empty($word))
                continue;
            if (stripos($p->post_title, $word) !== FALSE) {
                $words_match++;
            }
        }
        if ($words_match > $_MATCH_THRESHOLD) {
            array_push($relatedPostsJSON, $p->ID);
            array_push($relatedPostsArray, $p);
        }
    }

    $tags = wp_get_post_tags($postId);

    if ($tags) {
        $tag_ids = array();
        foreach ($tags as $individual_tag) {
            if (!in_array($individual_tag->term_id, $ignoreTags)) {
                $tag_ids[] = $individual_tag->term_id;
            }
        }
        $args = array(
            'tag__in' => $tag_ids,
            'post__not_in' => array($postId),
            'posts_per_page' => $noPosts,
            'category__not_in' => $ignoreCats,
            'tag__not_in' => $ignoreTags
        );
        
        $relatedPosts = get_posts($args);

        foreach ($relatedPosts as $post) {
            if (!in_array($post->ID, $relatedPostsJSON)) {
                array_push($relatedPostsJSON, $post->ID);
                array_push($relatedPostsArray, $post);
            }
        }
    }

    $cats = wp_get_post_categories($postId, array('fields' => 'ids'));
    if ($cats) {
        $cat_ids = array();
        foreach ($cats as $cat) {
            if (is_int($cat)) {
                array_push($cat_ids, $cat);
            } else {
                if (!in_array($cat->cat_ID, $ignoreCats)) {
                    array_push($cat_ids, $cat->cat_ID);
                }
            }
        }
        $args = array(
            'category__in' => $cat_ids,
            'post__not_in' => array($postId),
            'posts_per_page' => $noPosts,
            'category__not_in' => $ignoreCats,
            'tag__not_in' => $ignoreTags
        );
        
        $relatedPosts = get_posts($args);

        foreach ($relatedPosts as $post) {
            if (!in_array($post->ID, $relatedPostsJSON)) {
                array_push($relatedPostsJSON, $post->ID);
                array_push($relatedPostsArray, $post);
            }
        }
    }
    switch ($method) {
        case 'json':
            return json_encode(array_slice($relatedPostsJSON, 0, $post_number));
        case 'array':
            return array_slice($relatedPostsArray, 0, $post_number);
    }
}

global $excluded_words;
$excluded_words = array("a", "able", "about", "above", "abst", "accordance", "according", "accordingly", "across", "act", "actually", "added", "adj", "affected", "affecting", "affects", "after", "afterwards", "again", "against", "ah", "all", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "an", "and", "announce", "another", "any", "anybody", "anyhow", "anymore", "anyone", "anything", "anyway", "anyways", "anywhere", "apparently", "approximately", "are", "aren", "arent", "arise", "around", "as", "aside", "ask", "asking", "at", "auth", "available", "away", "awfully", "b", "back", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "beginning", "beginnings", "begins", "behind", "being", "believe", "below", "beside", "besides", "between", "beyond", "biol", "both", "brief", "briefly", "but", "by", "c", "ca", "came", "can", "cannot", "can't", "cause", "causes", "certain", "certainly", "co", "com", "come", "comes", "contain", "containing", "contains", "could", "couldnt", "d", "date", "did", "didn't", "different", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "due", "during", "e", "each", "ed", "edu", "effect", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "especially", "et", "et-al", "etc", "even", "ever", "every", "everybody", "everyone", "everything", "everywhere", "ex", "except", "f", "far", "few", "ff", "fifth", "first", "five", "fix", "followed", "following", "follows", "for", "former", "formerly", "forth", "found", "four", "from", "further", "furthermore", "g", "gave", "get", "gets", "getting", "give", "given", "gives", "giving", "go", "goes", "gone", "got", "gotten", "h", "had", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "hed", "hence", "her", "here", "hereafter", "hereby", "herein", "heres", "hereupon", "hers", "herself", "hes", "hi", "hid", "him", "himself", "his", "hither", "home", "how", "howbeit", "however", "hundred", "i", "id", "ie", "if", "i'll", "im", "immediate", "immediately", "importance", "important", "in", "inc", "indeed", "index", "information", "instead", "into", "invention", "inward", "is", "isn't", "it", "itd", "it'll", "its", "itself", "i've", "j", "just", "k", "keep keeps", "kept", "kg", "km", "know", "known", "knows", "l", "largely", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "lets", "like", "liked", "likely", "line", "little", "'ll", "look", "looking", "looks", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "me", "mean", "means", "meantime", "meanwhile", "merely", "mg", "might", "million", "miss", "ml", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "mug", "must", "my", "myself", "n", "na", "name", "namely", "nay", "nd", "near", "nearly", "necessarily", "necessary", "need", "needs", "neither", "never", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "nor", "normally", "nos", "not", "noted", "nothing", "now", "nowhere", "o", "obtain", "obtained", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "omitted", "on", "once", "one", "ones", "only", "onto", "or", "ord", "other", "others", "otherwise", "ought", "our", "ours", "ourselves", "out", "outside", "over", "overall", "owing", "own", "p", "page", "pages", "part", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "poorly", "possible", "possibly", "potentially", "pp", "predominantly", "present", "previously", "primarily", "probably", "promptly", "proud", "provides", "put", "q", "que", "quickly", "quite", "qv", "r", "ran", "rather", "rd", "re", "readily", "really", "recent", "recently", "ref", "refs", "regarding", "regardless", "regards", "related", "relatively", "research", "respectively", "resulted", "resulting", "results", "right", "run", "s", "said", "same", "saw", "say", "saying", "says", "sec", "section", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sent", "seven", "several", "shall", "she", "shed", "she'll", "shes", "should", "shouldn't", "show", "showed", "shown", "showns", "shows", "significant", "significantly", "similar", "similarly", "since", "six", "slightly", "so", "some", "somebody", "somehow", "someone", "somethan", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specifically", "specified", "specify", "specifying", "still", "stop", "strongly", "sub", "substantially", "successfully", "such", "sufficiently", "suggest", "sup", "sure", "a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", "aren't", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "can't", "cannot", "could", "couldn't", "did", "didn't", "do", "does", "doesn't", "doing", "don't", "down", "during", "each", "few", "for", "from", "further", "had", "hadn't", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "isn't", "it", "it's", "its", "itself", "let's", "me", "more", "most", "mustn't", "my", "myself", "no", "nor", "not", "of", "off", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "wasn't", "we", "we'd", "we'll", "we're", "we've", "were", "weren't", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "won't", "would", "wouldn't", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves");
