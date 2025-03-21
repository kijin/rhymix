<?php
$lang->board = 'Board';
$lang->except_notice = 'Exclude Notices';
$lang->use_bottom_list = 'Display Bottom List';
$lang->customize_bottom_list = 'Customize Bottom List';
$lang->use_anonymous = 'Use Anonymous';
$lang->anonymous_except_admin = 'Anonymous Except Admin';
$lang->anonymous_name = 'Anonymous Name for Display';
$lang->cmd_manage_menu = 'Manage Menus';
$lang->list_target_item = 'Target Item';
$lang->list_display_item = 'Display Item';
$lang->summary = 'Summary';
$lang->thumbnail = 'Thumbnail';
$lang->last_post = 'Last Post';
$lang->board_management = 'Board Management';
$lang->search_result = 'Search Result';
$lang->consultation = 'Consultation';
$lang->use_consultation = 'Use as Consultation Board';
$lang->secret = 'Secret';
$lang->thisissecret = 'This is a secret post.';
$lang->admin_mail = 'Administrator\'s Mail';
$lang->update_log = 'Update Log';
$lang->last_updater = 'Latest Update by';
$lang->cmd_board_list = 'Boards List';
$lang->cmd_module_config = 'Common Board Settings';
$lang->cmd_board_info = 'Board Info';
$lang->cmd_list_setting = 'List Settings';
$lang->cmd_list_items = 'Displayed Items and Order';
$lang->cmd_create_board = 'Create a New Board';
$lang->cmd_manage_selected_board = 'Manage Selected Board';
$lang->about_layout_setup = 'You can manually modify the board layout code. Insert or manage the widget code anywhere you want.';
$lang->about_board_category = 'You can make board categories. When a board category is broken, try rebuilding the cache file manually.';
$lang->about_except_notice = 'Notices will not be displayed in the normal list.<br />Caution: Using this option may increase DB load if you have many visitors and lots of posts.';
$lang->about_use_bottom_list = 'Display the list at the bottom when viewing a post.';
$lang->about_customize_bottom_list = 'Exact calculations of the lists consume a lot of server resources.<br />You can save server resources by calculating it roughly.<br />This should have no effect on SEO.';
$lang->about_use_anonymous_part1 = 'Hide the author\'s nickname to turn this board into an anonymous board.';
$lang->about_use_anonymous_part2 = 'It is more useful if you hide the nickname in the skin, as well.<br>Please concurrently turn off post history, which may unveil the author\'s information.';
$lang->about_anonymous_except_admin = 'The administrator\'s nickname will not be hidden.';
$lang->about_anonymous_name = 'You can customize the anonymous name that will be displayed instead of the author\'s nickname.<br><b>$NUM</b> will be replaced with a random number that is unique to each member. (e.g. anon_$NUM → anon_12345678)<br><b>$DAILYNUM</b> will be replaced with a random number that is unique to each member but changes every day.<br><b>$DOCNUM</b> will be replaced with a random number that is unique to each member and changes from post to post.<br><b>$DOCDAILYNUM</b> will be replaced with a random number that is unique to each member and changes every day from post to post.<br>You can append a number to each variable, like <strong>$DAILYNUM:5</strong> to control the number of digits from 1 to 8.<br>To use hexadecimal digits that include some alphabets, use <strong>STR</strong> instead of <strong>NUM</strong>.';
$lang->about_board = 'This module is for creating and managing boards.';
$lang->about_consultation = 'Members who are not managers will only see their own posts.<br>When this feature is enabled, non-members cannot read or write any posts on this board.';
$lang->about_secret = 'Users will be able to write secret posts or comments.';
$lang->about_admin_mail = 'A mail will be sent when a post or comment is submitted. Mails can be sent to mutiple mail addresses if connecting addresses with commas(,).';
$lang->about_list_config = 'If using list-style skin, you may arrange items to display. However, this feature might not be availble for non-official skins. If you double-click target items and display items, then you can add / remove them';
$lang->about_use_status = 'Please select status that can be selected when you write a post.';
$lang->about_protect_comment = 'Prevent updating or deleting a comment if it has children.';
$lang->about_update_log = 'Store a log of every version of a post every time it is updated.';
$lang->skip_bottom_list_for_olddoc = 'Do not calculate the bottom list exactly when viewing an old post.';
$lang->skip_bottom_list_for_robot = 'Do not calculate the bottom list exactly when a robot is visiting.';
$lang->msg_not_enough_point = 'Your point is not enough to write a post in this board.';
$lang->write_comment = 'Write a comment';
$lang->msg_not_allow_comment = 'This post is not allowed to write comment.';
$lang->no_board_instance = 'There is no board created.';
$lang->choose_board_instance = 'Please choose one or more board instance.';
$lang->comment_status = 'Comments Allowed';
$lang->category_settings = 'Category settings';
$lang->hide_category = 'Hide categories';
$lang->about_hide_category = 'Temporarily disable categories. Existing categories will not be deleted.<br>Unless you select the option below, users may not be able to write anymore.';
$lang->allow_no_category = 'Do not require category';
$lang->about_allow_no_category = 'Allow posting without selecting a category.<br>Admins are never required to select a category.';
$lang->protect_content = 'Protect Content';
$lang->protect_comment = 'Protect Comment';
$lang->protect_admin_content = 'Protect Admin Content';
$lang->protect_regdate = 'Update/Delete Time Limit';
$lang->filter_specialchars = 'Block Abuse of Unicode Symbols';
$lang->document_length_limit = 'Limit Post Length';
$lang->comment_length_limit = 'Limit Comment Length';
$lang->inline_data_url_limit = 'Limit Data URLs';
$lang->about_document_length_limit = 'Restrict posts that are too large. This limit may be triggered by pasting content that contains a lot of unnecessary markup.<br>This setting has no effect on the administrator and board managers.';
$lang->about_comment_length_limit = 'Restrict comments that are too large.<br>This setting has no effect on the administrator and board managers.';
$lang->about_inline_data_url_limit = 'Restrict data: URLs that can be used to evade file size limits or cause processing issues.<br>This setting also applies to the administrator and board managers.';
$lang->update_order_on_comment = 'Update Post on New Comment';
$lang->about_update_order_on_comment = 'When a new comment is posted, update the update timestamp of the parent post. This is needed for forums.';
$lang->about_filter_specialchars = 'Prevent use of excessive Unicode accents, RLO characters, and other symbols that hinder readability.';
$lang->about_protect_regdate = 'Prevent updating or deleting a post or comment after a certain amount of time has passed. (Unit: day)';
$lang->about_protect_content = 'Prevent updating a post if there are comments on it.';
$lang->about_protect_admin_content = 'Prevent updating or deleting a post or comment written by the administrator, even by a user who is permitted to manage the board.';
$lang->msg_protect_delete_content = 'You cannot delete a post with comments on it.';
$lang->msg_protect_update_content = 'You cannot update a post with comments on it.';
$lang->msg_admin_document_no_modify = 'You cannot edit the administrator\'s post.';
$lang->msg_admin_comment_no_modify = 'You cannot edit the administrator\'s comment.';
$lang->msg_board_delete_protect_comment = 'You cannot delete a comment when there are replies.';
$lang->msg_board_update_protect_comment = 'You cannot update a comment when there are replies.';
$lang->msg_protect_regdate_document = 'You cannot update or delete a post after %d days.';
$lang->msg_protect_regdate_comment = 'You cannot update or delete a comment after %d days.';
$lang->msg_dont_have_update_log = 'This post has no update log.';
$lang->msg_content_too_long = 'The content is too long.';
$lang->msg_data_url_restricted = 'The content has been restricted due to excessively large data URLs (such as inline images).';
$lang->original_letter = 'Original';
$lang->msg_warning_update_log = '<span class="x_label x_label-important">Warning!</span> This can massively increase the size of your database.';
$lang->reason_update = 'Reason for the update';
$lang->msg_no_update_id = 'The update ID field is mandatory.';
$lang->msg_no_update_log = 'There is no log for updates.';
$lang->cmd_modify_by_update_log = 'Modify this post with this log';
$lang->msg_admin_update_log = 'This post has been edited by the administrator. Please refer to the administrator.';
$lang->msg_update_log_revert = 'Are you sure to revert the post to this version?';
$lang->write_admin = 'Written by the administrator';
$lang->revert_reason_update = 'Revert to this version';
$lang->document_force_to_move = 'Delete to Recycle Bin';
$lang->about_document_force_to_move = 'When a post is deleted, depositing it in Recycle Bin instead of deleting it permamently.';
$lang->comment_delete_message = 'Leave Placeholder for Deleted Comment';
$lang->about_comment_delete_message = 'When a comment is deleted, leave a placeholder saying that it has been deleted.';
$lang->cmd_only_p_comment = 'Only if there are replies';
$lang->cmd_all_comment_message = 'Always';
$lang->cmd_do_not_message = 'Never';
$lang->delete_placeholder = 'Delete Placeholder';
$lang->msg_document_notify_mail = '[%s] The new post : %s';
$lang->cmd_document_vote_user = 'Upvoted by';
$lang->cmd_comment_vote_user = 'Upvoted by';
$lang->msg_not_target = 'You can only see the referrers\' lists for posts and comments.';
$lang->cmd_board_combined_board = 'Combined Board';
$lang->about_board_combined_board = 'You can use this board to view posts from other boards. Press the Ctrl key and click to select multiple boards.<br /><span style="color:red">Warning: permissions for the current board will apply to all affected posts and comments.</span>';
$lang->cmd_board_include_modules = 'Include Boards';
$lang->cmd_board_include_modules_none = '(None)';
$lang->cmd_board_include_days = 'Include Duration';
$lang->about_board_include_days = 'Only combine recent posts. If this value is set to 0, all posts from selected boards will be combined.<br />Durations shorter than 1 day can be set as fractions of a day, e.g. 0.25 days = 6 hours.';
$lang->cmd_board_include_notice = 'Include Notices';
