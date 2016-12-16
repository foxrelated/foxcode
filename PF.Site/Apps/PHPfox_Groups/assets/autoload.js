$Core.Groups = {
    setAsCover : function(iPageId, iPhotoId)
    {
        $.ajaxCall('PHPfox_Groups.setCoverPhoto', 'page_id=' + iPageId + '&photo_id=' + iPhotoId);
    },

    removeCover : function(iPageId)
    {
        $Core.jsConfirm({}, function() {
            $.ajaxCall('PHPfox_Groups.removeCoverPhoto', 'page_id=' + iPageId);
        }, function(){});
    }
};
$Behavior.groupsBuilder = function(){
    var sHash = 'js_groups_block_' + window.location.hash.replace('#','');
    if (sHash.length > 0)
    {
        $('.page_section_menu_header a').each(function()
        {
            if ($(this).attr('rel') == sHash)
            {
                var oObj = $(this);
                setTimeout(function(){
                    $('.active').removeClass('active');
                    $(oObj).parent().addClass('active');
                    $('.js_groups_block').hide();
                    $('#' + sHash).show();
                }, 600);
            }
        });
    }


    // Creating/Editing groups
    if ($Core.exists('#js_groups_add_holder')){

        $('.groups_type_add_inner_link').click(function(){

            $('.groups_type_add_form').hide();
            $('.groups_type_add_inner_link').show().addClass('unfocus');

            $('.groups_type_add_inner_link.focus').removeClass('focus');

            $(this).addClass('focus').removeClass('unfocus');

            $(this).parent().find('.groups_type_add_form').show();
            $(this).parent().find('.groups_type_add_input:first').focus();

            return false;
        });

        $('.groups_type_add_input').focus(function(){
            if (!$(this).hasClass('is_in_focus')){
                $(this).addClass('is_in_focus');
                $(this).val('');
            }
        })

        $('.groups_type_add_form_holder form').submit(function(){
            $Core.processForm('#js_groups_add_submit_button');
            $(this).ajaxCall('PHPfox_Groups.add');
            return false;
        });

        $('.groups_add_category select').change(function(){
            $(this).parent().parent().find('.js_groups_add_sub_category').hide();
            $(this).parent().parent().find('#js_groups_add_sub_category_' + $(this).val()).show();
            $('#js_category_groups_add_holder').val($(this).parent().parent().find('#js_groups_add_sub_category_' + $(this).val() + ' option:first').val());
        });

        $('.js_groups_add_sub_category select').change(function(){
            $('#js_category_groups_add_holder').val($(this).val());
        });

        $('#js_groups_add_change_photo').click(function(){
            $('#js_event_current_image').hide();
            $('#js_event_upload_image').show();
            $('#js_submit_upload_image').show();
            return false;
        });
    }
};

$Behavior.contentHeight = function()
{
    $('#content').height($('.main_timeline').height());
}

$Behavior.fixSizeTinymce = function()
{
    //The magic code to add show/hide custom event triggers
    (function ($)
    {
        $.each(['show', 'hide'], function (i, ev)
        {
            var el = $.fn[ev];
            $.fn[ev] = function ()
            {
                this.trigger(ev);
                return el.apply(this, arguments);
            };
        });
    })(jQuery);

    $('#js_groups_block_info').on('show', function()
    {
        $('.mceIframeContainer.mceFirst.mceLast iframe').height('275px');
    });
};
