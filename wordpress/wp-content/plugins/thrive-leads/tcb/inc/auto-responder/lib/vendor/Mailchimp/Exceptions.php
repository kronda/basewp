<?php

class Thrive_Api_Mailchimp_Error extends Exception {}
class Thrive_Api_Mailchimp_HttpError extends Thrive_Api_Mailchimp_Error {}

/**
 * The parameters passed to the API call are invalid or not provided when required
 */
class Thrive_Api_Mailchimp_ValidationError extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_ServerError_MethodUnknown extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_ServerError_InvalidParameters extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Unknown_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Request_TimedOut extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Zend_Uri_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_PDOException extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Avesta_Db_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_XML_RPC2_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_XML_RPC2_FaultException extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Too_Many_Connections extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Parse_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_Unknown extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_Disabled extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_DoesNotExist extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_NotApproved extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_ApiKey extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_UnderMaintenance extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_AppKey extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_IP extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_DoesExist extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_InvalidRole extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_InvalidAction extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_MissingEmail extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_CannotSendCampaign extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_MissingModuleOutbox extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_ModuleAlreadyPurchased extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_ModuleNotPurchased extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_User_NotEnoughCredit extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_MC_InvalidPayment extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_DoesNotExist extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidInterestFieldType extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidOption extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidUnsubMember extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidBounceMember extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_AlreadySubscribed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_NotSubscribed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidImport extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_MC_PastedList_Duplicate extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_MC_PastedList_InvalidImport extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Email_AlreadySubscribed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Email_AlreadyUnsubscribed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Email_NotExists extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Email_NotSubscribed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_MergeFieldRequired extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_CannotRemoveEmailMerge extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_Merge_InvalidMergeID extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_TooManyMergeFields extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidMergeField extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_InvalidInterestGroup extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_List_TooManyInterestGroups extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_DoesNotExist extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_StatsNotAvailable extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidAbsplit extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidContent extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidOption extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidStatus extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_NotSaved extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidSegment extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidRss extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidAuto extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_MC_ContentImport_InvalidArchive extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_BounceMissing extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Campaign_InvalidTemplate extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_EcommOrder extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Absplit_UnknownError extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Absplit_UnknownSplitTest extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Absplit_UnknownTestType extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Absplit_UnknownWaitUnit extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Absplit_UnknownWinnerType extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Absplit_WinnerNotSelected extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_Analytics extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_DateTime extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_Email extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_SendType extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_Template extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_TrackingOptions extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_Options extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_Folder extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_URL extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Module_Unknown extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_MonthlyPlan_Unknown extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Order_TypeUnknown extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_PagingLimit extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Invalid_PagingStart extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Max_Size_Reached extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_MC_SearchException extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Goal_SaveFailed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Conversation_DoesNotExist extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Conversation_ReplySaveFailed extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_File_Not_Found_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Folder_Not_Found_Exception extends Thrive_Api_Mailchimp_Error {}

/**
 * None
 */
class Thrive_Api_Mailchimp_Folder_Exists_Exception extends Thrive_Api_Mailchimp_Error {}


