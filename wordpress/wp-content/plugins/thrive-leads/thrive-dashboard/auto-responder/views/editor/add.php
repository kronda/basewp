<h3>Step 1: Choose Connection Type</h3>
<div class="tve_clear" style="height:20px;"></div>

<p>Choose whether you would like to connect using HTML form code or through an established API connection ?</p>

<div class="center" style="text-align: center">
    <select class="" id="connection-type" style="width: 250px;">
        <?php foreach ($connection_types as $connection_key => $connection_name) : ?>
            <option value="<?php echo $connection_key ?>"><?php echo $connection_name ?></option>
        <?php endforeach; ?>
    </select>

    <div class="clear" style="height: 20px;"></div>
    <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_success"
       data-ctrl="function:auto_responder.connection_form" data-step2="1">
        <span>Go to the next step</span>
    </a>
</div>