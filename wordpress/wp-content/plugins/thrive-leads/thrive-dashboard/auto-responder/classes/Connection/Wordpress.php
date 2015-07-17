<?php


class Thrive_List_Connection_Wordpress extends Thrive_List_Connection_Abstract
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return 'WordPress user account';
    }

    /**
     * this requires a special naming here, as it's about wordpress roles, not lists of subscribers
     *
     * @return string
     */
    public function getListSubtitle()
    {
        return 'Choose the role which should be assigned to your subscribers';
    }


    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('wordpress');
    }

    /**
     * just save the key in the database
     *
     * @return mixed|void
     */
    public function readCredentials()
    {
        $this->setCredentials(array('e' => true));

        $this->success('WordPress user account integration activated.');
        /**
         * finally, save the connection details
         */
        $this->save();
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        /**
         * wordpress integration is always supported
         */
        return true;
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        // no API instance needed here
        return null;
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array
     */
    protected function _getLists()
    {
        $roles = array();

        foreach (get_editable_roles() as $key => $role_data) {
            $roles[] = array(
                'id' => $key,
                'name' => $role_data['name']
            );
        }

        return $roles;
    }

    /**
     * add a contact to a list
     *
     * @param mixed $list_identifier
     * @param array $arguments
     * @return mixed
     */
    public function addSubscriber($list_identifier, $arguments)
    {
        /**
         * this sends a confirmation mail also to the user
         */
        $user_id = register_new_user($arguments['email'], $arguments['email']);

        if ($user_id instanceof WP_Error) {
            return $user_id->get_error_message();
        }

        /**
         * also, assign the selected role to the newly created user
         */
        $user = new WP_User($user_id);
        $user->set_role($list_identifier);

        return true;

    }

}