<?php

/**
 * erpAdminNotices.php
 *
 * @package   @todo
 * @author    Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @link      @todo
 * @copyright 2014 Panagiotis Vagenas <pan.vagenas@gmail.com>
 */

/**
 * Display message in admin panel as WP admin notices
 * 
 * @package Easy Related Posts Core Helpers
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class erpAdminNotices {

    /**
     * Instance of this class.
     *
     * @since 2.0.0
     * @var erpAdminNotices
     */
    protected static $instance = null;

    /**
     * Unique identifier for this plugin.
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since 2.0.0
     * @var string
     */
    protected $plugin_slug = ERP_SLUG;
    
    /**
     * Name of the array that will be stored in DB
     * @var string 
     */
    protected $optionsArrayName;
    /**
     * Options array as loaded from DB
     * @var array 
     */
    protected $options;

    /**
     * Costructor
     */
    private function __construct() {
        $this->optionsArrayName = $this->plugin_slug . 'AdminNotices';
        $this->getOptions();
        add_action( 'admin_notices', array($this, 'displayNotices') );
    }
    /**
     * Diplay notices. This is an action hoooked function, not to
     * be called directly
     */
    public function displayNotices() {
        foreach ($this->options as $key => $value) {
            if($this->shouldBeDiplayed($value)){
                echo $value->getContent();
            }
        }
        $this->storeOptions();
    }
    /**
     * Checks if the message should be displayed based
     * on message options
     * @param erpAdminMessage $message
     * @return boolean true if message have to be diplayed
     */
    private function shouldBeDiplayed(erpAdminMessage $message) {
        $displayIt = $this->isScreen($message->getScreen()) || $message->getScreen() == 'anywhere';
        
        $users = $message->getUsers();
        $relToUsers = empty($users);
        if(!$relToUsers){
            if(!array_key_exists(get_current_user_id(), $relToUsers)){
                return false;
            }
            $displayedToUsers = $message->getDisplayedToUsers();
            $curUserID = get_current_user_id();
            if(isset($displayedToUsers[$curUserID]) && $displayedToUsers[$curUserID] > $message->getTimes()){
                return false;
            }
        } elseif($message->getTimes() <= $message->getDisplayedTimes()){
            $this->deleteMessage($message->getId());
            return false;
        }
        
        return $displayIt;
    }
    /**
     * Checks if we load the defined screen
     * @param type $screenId
     * @return type true if screens match
     */
    private function isScreen($screenId) {
        $screen = get_current_screen();
        return $screenId == $screen->id;
    }

    /**
     * Return an instance of this class.
     *
     * @since 2.0.0
     * @return erpAdminNotices
     */
    public static function getInstance() {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Loads options array from DB and returns them
     * @return array options array
     */
    private function getOptions() {
        if(!is_array($this->options)){
            $this->options = get_option($this->optionsArrayName);
            if($this->options === false){
                $this->options = array();
                $this->storeOptions();
            }
        }
        return $this->options;
    }
    
    /**
     * Stores options in DB
     */
    private function storeOptions() {
        update_option($this->optionsArrayName, $this->options);
    }

    /**
     * Deletes a message from DB
     * @param int $mesId The message unique id
     */
    public function deleteMessage($mesId) {
        foreach ($this->options as $key => $value) {
            if($value->getId() === $mesId){
                unset($this->options[$key]);
                $this->storeOptions();
                break;
            }
        }
    }
    
    /**
     * Adds a message to be displayed
     * @param type $content The content of the messsage
     * @param type $type The type of the message
     * @param type $times How many times the message should be displayed. Default 1
     * @param type $screen The screen that this message should appear in. Default anywhere
     * @param type $users Array of users that this message should be displayed. Default empty, displays it to all users
     */
    public function addMessage($content, $type, $times = 1, $screen = 'anywhere', $users = array()) {
        $this->options[] = $message;
        $this->storeOptions();
    }
}


class erpAdminMessage{
    private $content;
    private $type;
    private $screen;
    private $id;
    private $times = 1;
    private $users = array();
    private $displayedTimes = 0;
    private $displayedToUsers = array();


    public function __construct($content, $type, $times = 1, $screen = 'anywhere') {
        $this->content = $content;
        $this->screen = $screen;
        $this->type = $type;
        $this->id = uniqid();
        $this->times = $times;
    }
    
    public function getContent() {
        $out = '<div class="'.$this->type.'">';
        $out .= '<p>' . $this->content . '</p>';
        $out .= '</div>';
        
        $this->displayedTimes++;
        
        if(array_key_exists(get_current_user_id(), $this->displayedToUsers)){
            $this->displayedToUsers[get_current_user_id()]++;
        } else {
            $this->displayedToUsers[get_current_user_id()] = 1;
        }
        
        return $out;
    }

    public function getType() {
        return $this->type;
    }

    public function getScreen() {
        return $this->screen;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function setType($type) {
        if($type != 'updated' && $type != 'error' && $type != 'update-nag'){
            return new WP_Error( 'erpError', __CLASS__ . ' -> ' . __FUNCTION__ . ' : Wrong message type');
        }
        $this->type = $type;
    }

    public function setScreen($screen) {
        $this->screen = $screen;
    }
    
    public function setScreenAnywhere() {
        $this->setScreen('anywhere');
    }

    public function getId(){
        return $this->id;
    }
    
    public function getTimes() {
        return $this->times;
    }

    public function getUsers() {
        return $this->users;
    }

    public function setTimes($times) {
        $this->times = $times;
    }

    public function setUsers($users) {
        $this->users = $users;
    }

    public function getDisplayedTimes() {
        return $this->displayedTimes;
    }

    public function getDisplayedToUsers() {
        return $this->displayedToUsers;
    }

    public function setDisplayedTimes($displayedTimes) {
        $this->displayedTimes = $displayedTimes;
    }

    public function setDisplayedToUsers($displayedToUsers) {
        $this->displayedToUsers = $displayedToUsers;
    }
}