var SUPER = Object.create(null);
(function($) {
    if(typeof super_common_i18n.ajaxurl === 'undefined'){
        super_common_i18n.duration = 500;
        super_common_i18n.ajaxurl = ajaxurl;
    }
    SUPER.debug_time = function($name){
        console.time($name);
    }
    SUPER.debug_time_end = function($name){
        console.timeEnd($name);
    }
    SUPER.debug = function($log){
    }
    SUPER.reCaptchaverifyCallback = function(response){
        $.ajax({
            type: 'post',
            url: super_common_i18n.ajaxurl,
            data: {
                action: 'super_verify_recaptcha',
                response: response,
            },
            success: function (data) {
                if(data==1){
                    $('.super-recaptcha').attr('data-verified',1);
                    $('.super-recaptcha').removeClass('error-active');
                    var $duration = parseFloat(super_common_i18n.duration);
                    $('.super-recaptcha').children('p').fadeOut($duration, function() {
                        $(this).remove();
                    });
                }else{
                    $('.super-recaptcha').attr('data-verified',0);
                }
            }
        }); 
    }
    function SUPERreCaptcha(){
        if($('.super-shortcode.super-field.super-recaptcha:not(.rendered)').length){
            if( (typeof grecaptcha === 'undefined') || (typeof grecaptcha.render === 'undefined') ) {
                $.getScript( 'https://www.google.com/recaptcha/api.js?onload=SUPERreCaptcha&render=explicit', function( data, textStatus, jqxhr ) {
                    SUPERreCaptchaRender();
                });
            }else{
               SUPERreCaptchaRender(); 
            }
        }
    }
    function SUPERreCaptchaRender(){
        $('.super-shortcode.super-field.super-recaptcha:not(.rendered)').each(function(){
            var $this = $(this);
            var $element = $this.find('.super-recaptcha');
            var $form = $this.parents('.super-form:eq(0)');
            var $form_id = $form.find('input[name="hidden_form_id"]').val();
            $element.attr('data-form',$form_id);
            $element.attr('id','super-recaptcha-'+$form_id);
            if($form.length==0){
                $this.html('<i>reCAPTCHA will only be generated and visible in the Preview or Front-end</i>');  
            }
            if($this.data('key')==''){
                $this.html('<i>reCAPTCHA API key and secret are empty, please navigate to:<br />Super Forms > Settings > Form Settings and fill out your reCAPTCHA API key and secret</i>');  
            }else{
                if(typeof $form_id !== 'undefined'){
                    var checkExist = setInterval(function() {
                        if( (typeof grecaptcha !== 'undefined') && (typeof grecaptcha.render !== 'undefined') ) {
                            clearInterval(checkExist);
                            $this.addClass('rendered');
                            var widgetId = grecaptcha.render('super-recaptcha-'+$form_id, {
                                'sitekey' : $element.data('key'),
                                'callback' : SUPER.reCaptchaverifyCallback,
                                'theme' : 'light'
                            });
                        }
                    }, 100);
                }
            }
        });
    }
    SUPER.generateBarcode = function(){
        $('.super-barcode').each(function(){
            var $this = $(this).find('input');
            var $renderer = 'css';
            var $barcode = $this.val();
            var $barcodetype = $this.data('barcodetype');
            var $background = $this.data('background');
            var $barcolor = $this.data('barcolor');
            var $barwidth = $this.data('barwidth');
            var $barheight = $this.data('barheight');
            var $modulesize = $this.data('modulesize');
            var $rectangular = $this.data('rectangular');
            var $quietzone = false;
            if ($this.data('quietzone')==1) $quietzone = true;
            var $settings = {
                output:$renderer,
                bgColor: $background,
                color: $barcolor,
                barWidth: $barwidth,
                barHeight: $barheight,
                moduleSize: $modulesize,
                addQuietZone: $quietzone
            };
            if($rectangular==1){
                $barcode = {code:$barcode, rect:true};
            }
            $this.parent().find('.super-barcode-target').barcode($barcode, $barcodetype, $settings);
        });
    }
    SUPER.rating = function(){
        $('.super-rating').on('mouseleave',function(){
            $(this).find('.super-rating-star').removeClass('active');
        });
        $('.super-rating-star').on('click',function(){
            $(this).parent().find('.super-rating-star').removeClass('selected');
            $(this).addClass('selected');
            $(this).prevAll('.super-rating-star').addClass('selected');
            var $rating = $(this).index()+1;
            $(this).parent().find('input').val($rating);
        });
        $('.super-rating-star').on('mouseover',function(){
            $(this).parent().find('.super-rating-star').removeClass('active');
            $(this).addClass('active');
            $(this).prevAll('.super-rating-star').addClass('active');
        });
    }
    SUPER.init_fileupload_fields = function(){
        $('.super-fileupload:not(.rendered)').each(function() {
            $(this).addClass('rendered');
            $(this).fileupload({
                filesContainer : $(this).find(".super-fileupload-files"),
                dropZone : $(this).parent('.super-field-wrapper'),
                add: function(e, data) {
                    var uploadErrors = [];
                    if(data.originalFiles[0]['size'] > ($(this).data('file-size')*1000000) ) {
                        $(this).parents('.super-field-wrapper:eq(0)').find('.super-fileupload-files').children('div[data-name="'+data.originalFiles[0]['name']+'"]').remove();
                        uploadErrors.push(super_common_i18n.errors.file_upload.filesize_too_big);
                    }
                    if(uploadErrors.length > 0) {
                        alert(uploadErrors.join("\n"));
                    }
                },
                dataType: 'json',
                autoUpload: false,
                maxFileSize: $(this).data('file-size')*1000000, // 5 MB
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $(this).parent().children('.super-progress-bar').css('display','block').css('width', progress + '%');
                }        
            }).on('fileuploaddone', function (e, data) {
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        var error = $('<span class="super-error"/>').text(' ('+file.error+')');
                        $(data.context.children()[index]).children('.super-error').remove();
                        $(data.context.children()[index]).append(error);
                        $(data.context.children()[index]).parent('div').addClass('error');
                    }else{
                        $(data.context).addClass('super-uploaded');
                        data.context.attr('data-name',file.name).attr('data-url',file.url).attr('data-thumburl',file.thumbnailUrl);
                    }
                });
            }).on('fileuploadadd', function (e, data) {
                $(this).removeClass('finished');
                $(this).parents('.super-field-wrapper:eq(0)').find('.super-fileupload-files > div.error').remove();
                data.context = $('<div/>').appendTo($(this).parents('.super-field-wrapper:eq(0)').find('.super-fileupload-files'));
                var accepted_file_types = $(this).data('accept-file-types');
                var file_types_object = accepted_file_types.split('|');
                $.each(data.files, function (index, file) {
                    var ext = file.name.split('.').pop();
                    if( (file_types_object.indexOf(ext)!=-1) || (accepted_file_types=='') ) {
                        data.context.parent('div').children('div[data-name="'+file.name+'"]').remove();
                        data.context.data(data).attr('data-name',file.name).html('<span class="super-fileupload-name">'+file.name+'</span><span class="super-fileupload-delete">[x]</span>');
                    }else{
                        data.context.remove();
                        alert(super_common_i18n.errors.file_upload.incorrect_file_extension);
                    }
                });
            }).on('fileuploadprocessalways', function (e, data) {
                var index = data.index;
                var file = data.files[index];
                if (file.error) {
                    $(this).parents('.super-field-wrapper:eq(0)').find('.super-fileupload-files').find("[data-name='" + file.name + "']").remove();
                    alert(file.error);
                }
            }).on('fileuploadfail', function (e, data) {
                $.each(data.files, function (index, file) {
                    var error = $('<span class="super-error"/>').text(' (file upload failed)');
                    $(data.context.children()[index]).children('.super-error').remove();
                    $(data.context.children()[index]).append(error);
                });
            }).on('fileuploadsubmit', function (e, data) {
                data.formData = {
                    'accept_file_types': $(this).data('accept-file-types'),
                    'max_file_size': $(this).data('file-size')*1000000,
                };
            });
        });
    }
    var distance_calculator_timeout = null; 
    SUPER.calculate_distance = function( $this ) {
        if($this.hasClass('super-distance-calculator')){
            var $form = $this.parents('.super-form:eq(0)');
            var $method = $this.data('distance-method');
            if($method=='start'){
                var $origin_field = $this;
                var $origin = $this.val();
                var $destination = $this.data('distance-destination');
                if($form.find('.super-shortcode-field[name="'+$destination+'"]').length){
                    var $destination_field = $form.find('.super-shortcode-field[name="'+$destination+'"]');
                    var $destination = $destination_field.val();
                }
            }else{
                var $origin_field = $form.find('.super-shortcode-field[name="'+$this.data('distance-start')+'"]');
                var $origin = $origin_field.val();
                var $destination_field = $this;
                var $destination = $this.val();
            }
            var $value = $origin_field.data('distance-value');
            var $units = $origin_field.data('distance-units');
            if($value!='dis_text'){
                var $units = 'metric';
            }
            if( ($origin=='') || ($destination=='') ) {
                return true;
            }
            if(distance_calculator_timeout !== null){
                clearTimeout(distance_calculator_timeout);
            }
            distance_calculator_timeout = setTimeout(function () {
                $this.parents('.super-field-wrapper:eq(0)').addClass('super-calculating-distance');
                $.ajax({
                    url: super_common_i18n.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'super_calculate_distance',
                        units: $units,
                        origin: $origin,
                        destination: $destination
                    },
                    success: function (result) {
                        var $result = jQuery.parseJSON(result);
                        if($result.status=='OK'){
                            var $leg = $result.routes[0].legs[0];
                            var $field = $origin_field.data('distance-field');
                            if( $value=='distance' ) {
                                var $calculation_value = $leg.distance.value
                            }
                            if( $value=='dis_text' ) {
                                var $calculation_value = $leg.distance.text
                            }
                            if( $value=='duration' ) {
                                var $calculation_value = $leg.duration.value
                            }
                            if( $value=='dur_text' ) {
                                var $calculation_value = $leg.duration.text
                            }
                            var $field = $form.find('.super-shortcode-field[name="'+$field+'"]');
                            $field.val($calculation_value);
                            SUPER.after_field_change_blur_hook($field);
                            SUPER.init_replace_html_tags();
                        }else{
                            if($result.status=='ZERO_RESULTS'){
                                var $alert_msg = super_common_i18n.errors.distance_calculator.zero_results;
                            }else{
                                if($result.status=='OVER_QUERY_LIMIT'){
                                    var $alert_msg = $result.error_message;
                                }else{
                                    var $alert_msg = super_common_i18n.errors.distance_calculator.error;
                                }
                            }
                            $('.super-msg').remove();
                            var $result = jQuery.parseJSON(result);
                            var $html = '<div class="super-msg super-error">';                            
                            $origin_field.blur();
                            $destination_field.blur();                             
                            $html += $alert_msg;
                            $html += '<span class="close"></span>';
                            $html += '</div>';
                            $($html).prependTo($form);
                            $('html, body').animate({
                                scrollTop: $form.offset().top-200
                            }, 1000);
                        }
                    },
                    complete: function(){
                        $this.parents('.super-field-wrapper:eq(0)').removeClass('super-calculating-distance');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert('Failed to process data, please try again');
                    }
                });
            }, 1000);
        }
    }
    SUPER.conditional_logic = function($changed_field, $form){
        if(typeof $form === 'undefined'){
            var $form = SUPER.get_frontend_or_backend_form();
        }
        if($form.hasClass('super-multipart')){
            var $form = $form.parents('.super-form:eq(0)');
        }
        if(typeof $changed_field !== 'undefined'){
            var $conditional_logic = $form.find('.super-conditional-logic[data-fields*="['+$changed_field.attr('name')+']"]');
            var $conditional_logic_with_tags = $form.find('.super-conditional-logic[data-tags*="['+$changed_field.attr('name')+']"]');
        }else{
            var $conditional_logic = $form.find('.super-conditional-logic');
        }
        var $did_loop = false;
        if(typeof $conditional_logic !== 'undefined'){
            if($conditional_logic.length!=0){
                $did_loop = true;
                SUPER.conditional_logic.loop($changed_field, $form, $conditional_logic);
            }
        }
        if(typeof $conditional_logic_with_tags !== 'undefined'){
            if($conditional_logic_with_tags.length!=0){
                $did_loop = true;
                SUPER.conditional_logic.loop($changed_field, $form, $conditional_logic_with_tags);
            }
        }
        if( $did_loop==false ) {
            SUPER.update_variable_fields($changed_field, $form);
        }
    }
    SUPER.return_dynamic_tag_value = function($parent, $value){
        if( typeof $value === 'undefined' ) return '';
        if( $value=='' ) return $value;
        if( (typeof $parent !== 'undefined') && ( ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-checkbox')) || ($parent.hasClass('super-countries')) ) ) {
            var $values = $value.toString().split(',');
            var $new_values = '';
            $.each($values, function( index, value ) {
                var $value = value.toString().split(';');
                $value = $value[0];
                if($new_values==''){
                    $new_values += $value;
                }else{
                    $new_values += ','+$value;
                }
            });
            $value = $new_values;
        }else{
            var $value = $value.toString().split(';');
            $value = $value[0];      
        }
        return $value;
    }
    SUPER.conditional_logic.loop = function($changed_field, $form, $conditional_logic){
        var $regular_expression = /\{(.*?)\}/g;
        $conditional_logic.each(function(){
            var $this = $(this);
            var $json = $this.val();
            var $wrapper = $this.parents('.super-shortcode:eq(0)');
            var $field = $wrapper.children('.super-shortcode-field');
            var $action = $wrapper.data('conditional_action');
            var $trigger = $wrapper.data('conditional_trigger');
            if(typeof $action !== 'undefined'){
                if($action!='disabled'){
                    var $conditions = jQuery.parseJSON($json);
                    if(typeof $conditions !== 'undefined'){
                        var $total = 0;
                        var $counter = 0;
                        $.each($conditions, function( index, v ) {
                            v.value = SUPER.update_variable_fields.replace_tags($form, $regular_expression, v.value);
                            v.value_and = SUPER.update_variable_fields.replace_tags($form, $regular_expression, v.value_and);
                            $total++;
                            var $shortcode_field = $form.find('.super-shortcode-field[name="'+v.field+'"]');
                            var $shortcode_field_value = $shortcode_field.val();
                            var $parent = $shortcode_field.parents('.super-shortcode:eq(0)');
                            if(typeof $shortcode_field_value === 'undefined') $shortcode_field_value = '';
                            $shortcode_field_value = SUPER.return_dynamic_tag_value($parent, $shortcode_field_value);
                            var $skip = false;
                            $shortcode_field.parents('.super-shortcode.super-column').each(function(){
                                if($(this).css('display')=='none') $skip = true;
                            });
                            if(v.and_method!=''){ 
                                var $shortcode_field_and = $form.find('.super-shortcode-field[name="'+v.field_and+'"]');
                                var $shortcode_field_and_value = $shortcode_field_and.val();
                                $shortcode_field_and.parents('.super-shortcode.super-column').each(function(){
                                    if($(this).css('display')=='none') $skip = true;
                                });
                                var $parent_and = $shortcode_field_and.parents('.super-shortcode:eq(0)');
                                if( ( $parent_and.css('display')=='none' ) && ( !$parent_and.hasClass('super-hidden') ) ) $skip = true;
                                $shortcode_field_and_value = SUPER.return_dynamic_tag_value($parent_and, $shortcode_field_and_value);
                            }
                            if(typeof $shortcode_field_and_value === 'undefined') $shortcode_field_and_value = '';
                            var $parent = $shortcode_field.parents('.super-shortcode:eq(0)');
                            if( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) $skip = true;
                            if( $skip==true ) {
                            }else{
                                if( (v.logic=='greater_than') || (v.logic=='less_than') || (v.logic=='greater_than_or_equal') || (v.logic=='less_than_or_equal') ) {
                                    if( ( $parent.hasClass('super-dropdown') ) || ( $parent.hasClass('super-countries') ) ){
                                        var $sum = 0;
                                        var $selected = $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)');
                                        $selected.each(function () {
                                            $sum += $(this).data('value');
                                        });
                                        v.value = $sum;
                                    }
                                    if( $parent.hasClass('super-checkbox') ) {
                                        var $sum = 0;
                                        var $checked = $parent.find('input[type="checkbox"]:checked');
                                        $checked.each(function () {
                                            $sum += $(this).val();
                                        });
                                        v.value = $sum;
                                    }
                                    if( $parent.hasClass('super-currency') ) {
                                        var $value = $shortcode_field.val();
                                        var $currency = $shortcode_field.data('currency');
                                        var $format = $shortcode_field.data('format');
                                        var $decimals = $shortcode_field.data('decimals');
                                        var $thousand_separator = $shortcode_field.data('thousand-separator');
                                        var $decimal_seperator = $shortcode_field.data('decimal-separator');
                                        $value = $value.replace($currency, '').replace($format, '');
                                        $value = $value.split($thousand_separator).join('');
                                        $value = $value.split($decimal_seperator).join('.');
                                        $shortcode_field_value = ($value) ? parseFloat($value) : 0;
                                    }
                                }
                                if(v.and_method!=''){ 
                                    if( (v.logic_and=='greater_than') || (v.logic_and=='less_than') || (v.logic_and=='greater_than_or_equal') || (v.logic_and=='less_than_or_equal') ) {
                                        if( ( $parent_and.hasClass('super-dropdown') ) || ( $parent_and.hasClass('super-countries') ) ) {
                                            var $sum = 0;
                                            var $selected = $parent_and.find('.super-dropdown-ui li.selected:not(.super-placeholder)');
                                            $selected.each(function () {
                                                $sum += $(this).data('value');
                                            });
                                            v.value_and = $sum;
                                        }
                                        if( $parent_and.hasClass('super-checkbox') ) {
                                            var $sum = 0;
                                            var $checked = $parent_and.find('input[type="checkbox"]:checked');
                                            $checked.each(function () {
                                                $sum += $(this).val();
                                            });
                                            v.value_and = $sum;
                                        }
                                        if( $parent.hasClass('super-currency') ) {
                                            var $value = $shortcode_field_and.val();
                                            var $currency = $shortcode_field_and.data('currency');
                                            var $format = $shortcode_field_and.data('format');
                                            var $decimals = $shortcode_field_and.data('decimals');
                                            var $thousand_separator = $shortcode_field_and.data('thousand-separator');
                                            var $decimal_seperator = $shortcode_field_and.data('decimal-separator');
                                            $value = $value.replace($currency, '').replace($format, '');
                                            $value = $value.split($thousand_separator).join('');
                                            $value = $value.split($decimal_seperator).join('.');
                                            $shortcode_field_and_value = ($value) ? parseFloat($value) : 0;
                                        }
                                    }
                                }
                                var $match_found = 0;
                                if( ( v.logic=='equal' ) && ( v.value==$shortcode_field_value ) ) $match_found++;
                                if( ( v.logic=='not_equal' ) && ( v.value!=$shortcode_field_value ) ) $match_found++;
                                if( ( v.logic=='greater_than' ) && ( parseFloat($shortcode_field_value)>parseFloat(v.value) ) ) $match_found++;
                                if( ( v.logic=='less_than' ) && ( parseFloat($shortcode_field_value)<parseFloat(v.value) ) ) $match_found++;
                                if( ( v.logic=='greater_than_or_equal' ) && ( parseFloat($shortcode_field_value)>=parseFloat(v.value) ) ) $match_found++;
                                if( ( v.logic=='less_than_or_equal' ) && ( parseFloat($shortcode_field_value)<=parseFloat(v.value) ) ) $match_found++;
                                if( v.and_method!='' ) {
                                    if( ( v.logic_and=='equal' ) && ( v.value_and==$shortcode_field_and_value ) ) $match_found++;
                                    if( ( v.logic_and=='not_equal' ) && ( v.value_and!=$shortcode_field_and_value ) ) $match_found++;
                                    if( ( v.logic_and=='greater_than' ) && ( parseFloat($shortcode_field_and_value)>parseFloat(v.value_and) ) ) $match_found++;
                                    if( ( v.logic_and=='less_than' ) && ( parseFloat($shortcode_field_and_value)<parseFloat(v.value_and) ) ) $match_found++;
                                    if( ( v.logic_and=='greater_than_or_equal' ) && ( parseFloat($shortcode_field_and_value)>=parseFloat(v.value_and) ) ) $match_found++;
                                    if( ( v.logic_and=='less_than_or_equal' ) && (parseFloat($shortcode_field_and_value)<=parseFloat(v.value_and) ) ) $match_found++;
                                }
                                if( v.logic=='contains' ) {
                                    if( ($parent.hasClass('super-checkbox')) || ($parent.hasClass('super-radio')) || ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ) {
                                        var $checked = $shortcode_field_value.split(',');
                                        var $string_value = v.value.toString();
                                        $.each($checked, function( index, value ) {
                                            if( value.indexOf($string_value) >= 0) {
                                                $match_found++;
                                                return false
                                            }
                                        });
                                    }else{
                                        if( $shortcode_field_value.indexOf(v.value) >= 0) $match_found++;
                                    }
                                }
                                if( v.and_method!='' ) {
                                    if( v.logic_and=='contains' ) {
                                        if( ($parent.hasClass('super-checkbox')) || ($parent.hasClass('super-radio')) || ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ) {
                                            var $checked = $shortcode_field_and_value.split(',');
                                            var $string_value = v.value_and.toString();
                                            $.each($checked, function( index, value ) {
                                                if( value.indexOf($string_value) >= 0) {
                                                    $match_found++;
                                                    return false
                                                }
                                            });
                                        }else{
                                            if( $shortcode_field_and_value.indexOf(v.value_and) >= 0) $match_found++;
                                        }
                                    }
                                }
                                if( v.and_method=='and' ) {
                                    if($match_found>=2) $counter++;
                                }else{
                                    if($match_found>=1) $counter++;
                                }
                            }
                        });
                        var $changed_wrappers = $();
                        if($trigger=='all'){
                            if($counter==$total){
                                if( ($action=='show') && ($wrapper.css('display')=='none') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','block');
                                }
                                if( ($action=='hide') && ($wrapper.css('display')=='block') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','none');
                                }
                            }else{
                                if( ($action=='show') && ($wrapper.css('display')=='block') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','none');
                                }
                                if( ($action=='hide') && ($wrapper.css('display')=='none') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','block');
                                }
                            }
                        }else{
                            if($counter!=0){
                                if( ($action=='show') && ($wrapper.css('display')=='none') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','block');
                                }
                                if( ($action=='hide') && ($wrapper.css('display')=='block') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','none');
                                }
                            }else{
                                if( ($action=='show') && ($wrapper.css('display')=='block') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','none');
                                }
                                if( ($action=='hide') && ($wrapper.css('display')=='none') ){
                                    $changed_wrappers = $changed_wrappers.add($wrapper);
                                    $wrapper.css('display','block');
                                }
                            }
                        }
                        $changed_wrappers.each(function(){
                            $(this).find('.super-shortcode-field').each(function(){
                                var $parent = $(this).parents('.super-shortcode:eq(0)');
                                var $element = $parent.find('div[data-fields]');
                                if(typeof $element !== 'undefined'){
                                    var $data_fields = $element.attr('data-fields');
                                    if(typeof $data_fields !== 'undefined'){
                                        $data_fields = $data_fields.split(']');
                                        $.each($data_fields, function( k, v ) {
                                            if(v!=''){
                                                v = v.replace('[','');
                                                var $field = $form.find('.super-shortcode-field[name="'+v+'"]');
                                                if(typeof $field !== 'undefined'){
                                                    SUPER.after_field_change_blur_hook($field);
                                                }
                                            }
                                        });
                                    }
                                }
                                var $element = $parent.find('div[data-tags]');
                                if(typeof $element !== 'undefined'){
                                    var $data_fields = $element.attr('data-tags');
                                    if(typeof $data_fields !== 'undefined'){
                                        $data_fields = $data_fields.split(']');
                                        $.each($data_fields, function( k, v ) {
                                            if(v!=''){
                                                v = v.replace('[','');
                                                var $field = $form.find('.super-shortcode-field[name="'+v+'"]');
                                                if(typeof $field !== 'undefined'){
                                                    SUPER.after_field_change_blur_hook($field);
                                                }
                                            }
                                        });
                                    }
                                }
                                SUPER.after_field_change_blur_hook($(this));
                            });
                        });
                    }
                }
            }
        });
        SUPER.update_variable_fields($changed_field, $form);
    }
    SUPER.update_variable_fields = function($changed_field, $form){
        if(typeof $changed_field !== 'undefined'){
            var $variable_fields = $form.find('.super-variable-conditions[data-fields*="['+$changed_field.attr('name')+']"]');
            var $variable_fields_with_tags = $form.find('.super-variable-conditions[data-tags*="['+$changed_field.attr('name')+']"]');
        }else{
            var $variable_fields = $form.find('.super-variable-conditions');
        }
        if(typeof $variable_fields !== 'undefined'){
            if($variable_fields.length!=0){
                SUPER.update_variable_fields.loop($changed_field, $form, $variable_fields);
            }
        }
        if(typeof $variable_fields_with_tags !== 'undefined'){
            if($variable_fields_with_tags.length!=0){
                SUPER.update_variable_fields.loop($changed_field, $form, $variable_fields_with_tags);
            }
        }
    }
    SUPER.update_variable_fields.replace_tags = function($form, $regular_expression, $v_value, $target){
        if(typeof $target === 'undefined') $target = null;
        var $array = [];
        var $value = '';
        var $i = 0;
        while (($match = $regular_expression.exec($v_value)) != null) {
            $array[$i] = $match[1];
            $i++;
        }
        for (var $i = 0; $i < $array.length; $i++) {
            var $name = $array[$i];
            if($name=='dynamic_column_counter'){
                if($target!=null){
                    $v_value = $target.parents('.super-duplicate-column-fields:eq(0)').index()+1;
                    return $v_value;
                }
            }
            var $old_name = $name;
            var $options = $name.toString().split(';');
            var $name = $options[0]; // this is the field name e.g: {option;2} the variable $name would contain: option
            var $value_type = 'var'; // return field value as 'var' or 'int' {field;2;var} to return varchar or {field;2;int} to return integer
            if(typeof $options[1] === 'undefined'){
                var $value_n = 0;
            }else{
                var $value_n = $options[1];
                if($value_n==1){
                    $value_n = 0;
                }
                if(typeof $options[2] !== 'undefined'){
                    if( ($options[2]!='var') && ($options[2]!='int') ) {
                        $value_type = 'var';
                    }else{
                        $value_type = $options[2];
                    }
                }
            }
            var $default_value = '';
            if($value_type=='int'){
                var $default_value = 0;
            }
            var $element = $form.find('.super-shortcode-field[name="'+$name+'"]');
            var $hidden = false;
            $element.parents('.super-shortcode.super-column').each(function(){
                if($(this).css('display')=='none'){
                    $hidden = true;
                }
            });
            var $parent = $element.parents('.super-shortcode:eq(0)');
            if( ( $hidden==true )  || ( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) ) {
                $v_value = $v_value.replace('{'+$name+'}', $default_value);
                $v_value = $v_value.replace('{'+$name+';label}', $default_value);
            }else{
                if( !$element.length ) {
                    $v_value = $v_value.replace('{'+$name+'}', $default_value);
                    $v_value = $v_value.replace('{'+$name+';label}', $default_value);
                }else{
                    var $text_field = true;
                    var $parent = $element.parents('.super-field:eq(0)');
                    if( ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ){
                        $text_field = false;
                        var $sum = '';
                        if($value_type=='int') var $sum = 0;
                        var $selected = $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)');
                        $selected.each(function () {
                            if($value_n=='label'){
                                var $new_value = $(this).text();
                            }else{
                                var $new_value = $(this).data('value').toString().split(';');
                                if($value_n==0){
                                    $new_value = $new_value[0];
                                }else{
                                    if(typeof $new_value[($value_n-1)]==='undefined'){
                                        $new_value = $new_value[0];
                                    }else{
                                        $new_value = $new_value[($value_n-1)];
                                    }
                                }
                            }
                            if(typeof $new_value==='undefined'){
                                $new_value = '';
                            }
                            if($value_type=='int'){
                                $sum += parseFloat($new_value);
                            }else{
                                if($sum==''){
                                    $sum += $new_value;
                                }else{
                                    $sum += ','+$new_value;
                                }
                            }
                        });
                        $value = $sum;
                    }
                    if($parent.hasClass('super-checkbox')){
                        $text_field = false;
                        var $checked = $parent.find('.super-field-wrapper > label.super-selected');
                        var $values = '';
                        $checked.each(function () {
                            if($value_n=='label'){
                                if($values==''){
                                    $values += $(this).text();
                                }else{
                                    $values += ', '+$(this).text();
                                }
                            }else{
                                if($values==''){
                                    $values += $(this).children('input').val();
                                }else{
                                    $values += ','+$(this).children('input').val();
                                }
                            }
                        });
                        var $sum = '';
                        if($value_type=='int') var $sum = 0;
                        if($value_n=='label'){
                            $sum += $values;
                        }else{
                            var $new_value_array = $values.toString().split(',');
                            $.each($new_value_array, function( k, v ) {
                                var v = v.toString().split(';');
                                if($value_n==0){
                                    $new_value = v[0];
                                }else{
                                    $new_value = v[($value_n-1)];
                                }
                                if(typeof $new_value==='undefined'){
                                    $new_value = '';
                                }
                                if($value_type=='int'){
                                    $sum += parseFloat($new_value);
                                }else{
                                    $sum += ($new_value);
                                }
                            });
                        }
                        $value = $sum;
                    }
                    if($parent.hasClass('super-radio')){
                        $text_field = false;
                        var $new_value = $element.val().toString().split(';');
                        if($value_n==0){
                            $new_value = $new_value[0];
                        }else{
                            $new_value = $new_value[($value_n-1)];
                        }
                        if(typeof $new_value==='undefined'){
                            $new_value = '';
                        }
                        if($value_n=='label'){
                            var $new_value = '';
                            $element.parents('.super-field:eq(0)').find('.super-field-wrapper .super-selected').each(function(){
                                $new_value = $(this).text();
                            });
                        }
                        if($value_type=='int'){
                            $value = parseFloat($new_value);
                        }else{
                            $value = ($new_value);
                        }
                    }
                    if($parent.hasClass('super-hidden')){
                        if($parent.attr('data-conditional_variable_action')=='enabled'){
                            $text_field = false;
                            var $new_value = $element.val().toString().split(';');
                            if($value_n==0){
                                $new_value = $new_value[0];
                            }else{
                                $new_value = $new_value[($value_n-1)];
                            }
                            if(typeof $new_value==='undefined'){
                                $new_value = '';
                            }
                            if($value_type=='int'){
                                $value = parseFloat($new_value);
                            }else{
                                $value = ($new_value);
                            }
                        }
                    }
                    if( $text_field==true ) {
                        if( $value_type=='int' ) {
                            $value = ($element.val()) ? parseFloat($element.val()) : '';
                        }else{
                            $value = $element.val();
                            if( $target ) {
                                if( (typeof $element.attr('data-value') !== 'undefined') && ($target.hasClass('super-html-content')) ) {
                                    $value = $element.attr('data-value');
                                }
                            }
                        }
                    }
                    if( ($value_type=='int') && (isNaN($value)) ) {
                        $value = $default_value;
                    }
                    $v_value = $v_value.replace('{'+$old_name+'}', $value);
                }
            }
        }
        return $v_value;
    }
    SUPER.update_variable_fields.loop = function($changed_field, $form, $variable_fields){
        var $regular_expression = /\{(.*?)\}/g;
        var $updated_variable_fields = {};
        $variable_fields.each(function(){
            var $this = $(this);
            var $wrapper = $this.parent('.super-shortcode');
            var $field = $wrapper.children('.super-shortcode-field');
            var $counter = 0;
            var $prev_match_found = false;
            var $conditions = jQuery.parseJSON($this.val());
            if(typeof $conditions !== 'undefined'){
                var $field_values = {};
                $.each($conditions, function( index, v ) {
                    v.variable_value = v.new_value
                    if(typeof $field_values[v.field] === 'undefined'){
                        var $shortcode_field = $form.find('.super-shortcode-field[name="'+v.field+'"]');
                        var $shortcode_field_value = $shortcode_field.val();
                        if(typeof $shortcode_field_value === 'undefined') $shortcode_field_value = '';
                        $field_values[v.field] = {};
                        $field_values[v.field].field = $shortcode_field;
                        $field_values[v.field].value = $shortcode_field_value;
                        $shortcode_field.parents('.super-shortcode.super-column').each(function(){
                            if($(this).css('display')=='none') {
                                $field_values[v.field].skip = true
                            }
                        });
                        var $parent = $shortcode_field.parents('.super-shortcode:eq(0)');
                        $field_values[v.field].parent = {};
                        $field_values[v.field].parent.element = $parent;
                        if( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) {
                            $field_values[v.field].skip = true
                        }
                        $field_values[v.field].parent.hasClass = {};
                        $field_values[v.field].parent.hasClass.checkbox = $parent.hasClass('super-checkbox');
                        $field_values[v.field].parent.hasClass.radio = $parent.hasClass('super-radio');
                        $field_values[v.field].parent.hasClass.dropdown = $parent.hasClass('super-dropdown');
                        $field_values[v.field].parent.hasClass.countries = $parent.hasClass('super-countries');
                    }else{
                        $shortcode_field = $field_values[v.field].field;
                        $shortcode_field_value = $field_values[v.field].value;
                        var $parent = $shortcode_field.parents('.super-shortcode:eq(0)');
                    }
                    $shortcode_field_value = SUPER.return_dynamic_tag_value($parent, $shortcode_field_value);
                    if( (typeof $field_values[v.field_and] === 'undefined') && ( v.and_method!='' ) ) {
                        var $shortcode_field_and = $form.find('.super-shortcode-field[name="'+v.field_and+'"]');
                        var $shortcode_field_and_value = $shortcode_field_and.val();
                        if(typeof $shortcode_field_and_value === 'undefined') $shortcode_field_and_value = '';
                        $field_values[v.field_and] = {};
                        $field_values[v.field_and].field = $shortcode_field_and;
                        $field_values[v.field_and].value = $shortcode_field_and_value;
                        $shortcode_field_and.parents('.super-shortcode.super-column').each(function(){
                            if($(this).css('display')=='none') {
                                $field_values[v.field_and].skip = true
                            }
                        });
                        var $parent = $shortcode_field_and.parents('.super-shortcode:eq(0)');
                        $field_values[v.field_and].parent = {};
                        $field_values[v.field_and].parent.element = $parent;
                        if( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) {
                            $field_values[v.field_and].skip = true
                        }
                        $field_values[v.field_and].parent.hasClass = {};
                        $field_values[v.field_and].parent.hasClass.checkbox = $parent.hasClass('super-checkbox');
                        $field_values[v.field_and].parent.hasClass.radio = $parent.hasClass('super-radio');
                        $field_values[v.field_and].parent.hasClass.dropdown = $parent.hasClass('super-dropdown');
                        $field_values[v.field_and].parent.hasClass.countries = $parent.hasClass('super-countries');
                        $shortcode_field_and_value = SUPER.return_dynamic_tag_value($parent, $shortcode_field_and_value);
                    }else{
                        if(typeof $field_values[v.field_and] !== 'undefined'){
                            $shortcode_field_and = $field_values[v.field_and].field;
                            $shortcode_field_and_value = $field_values[v.field_and].value;
                            var $parent = $shortcode_field_and.parents('.super-shortcode:eq(0)');
                            $shortcode_field_and_value = SUPER.return_dynamic_tag_value($parent, $shortcode_field_and_value);
                        }
                    }
                    if( ( (typeof $field_values[v.field] !== 'undefined') && ($field_values[v.field].skip==true) ) || ( (typeof $field_values[v.field_and] !== 'undefined') && ($field_values[v.field_and].skip==true) ) ) {
                    }else{
                        v.variable_value = SUPER.update_variable_fields.replace_tags($form, $regular_expression, v.variable_value);
                        v.value = SUPER.update_variable_fields.replace_tags($form, $regular_expression, v.value);
                        v.value_and = SUPER.update_variable_fields.replace_tags($form, $regular_expression, v.value_and);
                        if( (v.logic=='greater_than') || (v.logic=='less_than') || (v.logic=='greater_than_or_equal') || (v.logic=='less_than_or_equal') ) {
                            var $parent = $field_values[v.field].parent.element;
                            if( ( $field_values[v.field].parent.hasClass.dropdown ) || ( $field_values[v.field].parent.hasClass.countries ) ){
                                var $sum = 0;
                                $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)').each(function () {
                                    $sum += $(this).data('value');
                                });
                                v.value = $sum;
                            }
                            if( $field_values[v.field].parent.hasClass.checkbox ) {
                                var $sum = 0;
                                $parent.find('input[type="checkbox"]:checked').each(function () {
                                    $sum += $(this).val();
                                });
                                v.value = $sum;
                            }
                        }
                        if( (v.logic_and=='greater_than') || (v.logic_and=='less_than') || (v.logic_and=='greater_than_or_equal') || (v.logic_and=='less_than_or_equal') ) {
                            if(typeof $field_values[v.field_and] !== 'undefined' ) {
                                var $parent = $field_values[v.field_and].parent.element;
                                if( ( $field_values[v.field_and].parent.hasClass.dropdown ) || ( $field_values[v.field_and].parent.hasClass.countries ) ){
                                    var $sum = 0;
                                    $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)').each(function () {
                                        $sum += $(this).data('value');
                                    });
                                    v.value_and = $sum;
                                }
                                if( $field_values[v.field_and].parent.hasClass.checkbox ) {
                                    var $sum = 0;
                                    $parent.find('input[type="checkbox"]:checked').each(function () {
                                        $sum += $(this).val();
                                    });
                                    v.value_and = $sum;
                                }
                            }
                        }
                        var $match_found = 0;
                        if( ( v.logic=='equal' ) && ( v.value==$shortcode_field_value ) ) $match_found++;
                        if( ( v.logic=='not_equal' ) && ( v.value!=$shortcode_field_value ) ) $match_found++;
                        if( ( v.logic=='greater_than' ) && ( parseFloat($shortcode_field_value)>parseFloat(v.value) ) ) $match_found++;
                        if( ( v.logic=='less_than' ) && ( parseFloat($shortcode_field_value)<parseFloat(v.value) ) ) $match_found++;
                        if( ( v.logic=='greater_than_or_equal' ) && ( parseFloat($shortcode_field_value)>=parseFloat(v.value) ) ) $match_found++;
                        if( ( v.logic=='less_than_or_equal' ) && ( parseFloat($shortcode_field_value)<=parseFloat(v.value) ) ) $match_found++;
                        if( v.and_method!='' ) {
                            if( ( v.logic_and=='equal' ) && ( v.value_and==$shortcode_field_and_value ) ) $match_found++;
                            if( ( v.logic_and=='not_equal' ) && ( v.value_and!=$shortcode_field_and_value ) ) $match_found++;
                            if( ( v.logic_and=='greater_than' ) && ( parseFloat($shortcode_field_and_value)>parseFloat(v.value_and) ) ) $match_found++;
                            if( ( v.logic_and=='less_than' ) && ( parseFloat($shortcode_field_and_value)<parseFloat(v.value_and) ) ) $match_found++;
                            if( ( v.logic_and=='greater_than_or_equal' ) && ( parseFloat($shortcode_field_and_value)>=parseFloat(v.value_and) ) ) $match_found++;
                            if( ( v.logic_and=='less_than_or_equal' ) && ( parseFloat($shortcode_field_and_value)<=parseFloat(v.value_and) ) ) $match_found++;
                        }
                        if( v.logic=='contains' ) {
                            var $parent = $field_values[v.field].parent.element;
                            if( ( $field_values[v.field].parent.hasClass.checkbox ) || 
                                ( $field_values[v.field].parent.hasClass.radio ) || 
                                ( $field_values[v.field].parent.hasClass.dropdown ) || 
                                ( $field_values[v.field].parent.hasClass.countries ) ) {
                                if(typeof $field_values[v.field].parent.element.split_checked === 'undefined') {
                                    $field_values[v.field].parent.element.split_checked = $shortcode_field_value.split(',');
                                }
                                var $string_value = v.value.toString();
                                $.each($field_values[v.field].parent.element.split_checked, function( index, value ) {
                                    if( value.indexOf($string_value) >= 0) {
                                        $match_found++;
                                        return false
                                    }
                                });
                            }else{
                                if( $shortcode_field_value.indexOf(v.value) >= 0) $match_found++;
                            }
                        }
                        if( v.and_method!='' ) {
                            if( v.logic_and=='contains' ) {
                                var $parent = $field_values[v.field_and].parent.element;
                                if( ( $field_values[v.field_and].parent.hasClass.checkbox ) || 
                                    ( $field_values[v.field_and].parent.hasClass.radio ) || 
                                    ( $field_values[v.field_and].parent.hasClass.dropdown ) || 
                                    ( $field_values[v.field_and].parent.hasClass.countries ) ) {
                                    if(typeof $field_values[v.field_and].parent.element.split_checked === 'undefined') {
                                        $field_values[v.field_and].parent.element.split_checked = $shortcode_field_and_value.split(',');
                                    }
                                    var $string_value = v.value_and.toString();
                                    $.each($field_values[v.field_and].parent.element.split_checked, function( index, value ) {
                                        if( value.indexOf($string_value) >= 0) {
                                            $match_found++;
                                            return false
                                        }
                                    });
                                }else{
                                    if( $shortcode_field_and_value.indexOf(v.value_and) >= 0) $match_found++;
                                }
                            }
                        }
                        if( v.and_method=='and' ) {
                            if($match_found>=2) {
                                $prev_match_found = true;
                                if( v.new_value!='' ) {
                                    var $array = [];
                                    var $value = '';
                                    var $i = 0;
                                    while (($match = $regular_expression.exec(v.new_value)) != null) {
                                        $array[$i] = $match[1];
                                        $i++;
                                    }
                                    for (var $i = 0; $i < $array.length; $i++) {
                                        var $name = $array[$i];
                                        var $element = $form.find('.super-shortcode-field[name="'+$name+'"]');
                                        var $hidden = false;
                                        $element.parents('.super-shortcode.super-column').each(function(){
                                            if($(this).css('display')=='none'){
                                                $hidden = true;
                                            }
                                        });
                                        var $parent = $element.parents('.super-shortcode:eq(0)');
                                        if( ( $hidden==true )  || ( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) ) {
                                            v.variable_value = v.variable_value.replace('{'+$name+'}', 0)
                                        }else{
                                            if( !$element.length ) {
                                                v.variable_value = v.variable_value.replace('{'+$name+'}', 0)
                                            }else{
                                                var $text_field = true;
                                                var $parent = $element.parents('.super-field:eq(0)');
                                                $value = $element.val();
                                                v.variable_value = v.variable_value.replace('{'+$name+'}', $value)
                                            }
                                        }
                                    }
                                }
                                $field.val(v.variable_value);
                            }else{
                                if($prev_match_found==false){
                                    $field.val('');
                                }
                            }
                        }else{
                            if($match_found>=1) {
                                $prev_match_found = true;
                                if( v.new_value!='' ) {
                                    var $array = [];
                                    var $value = '';
                                    var $i = 0;
                                    while (($match = $regular_expression.exec(v.new_value)) != null) {
                                        $array[$i] = $match[1];
                                        $i++;
                                    }
                                    for (var $i = 0; $i < $array.length; $i++) {
                                        var $name = $array[$i];
                                        var $element = $form.find('.super-shortcode-field[name="'+$name+'"]');
                                        var $hidden = false;
                                        $element.parents('.super-shortcode.super-column').each(function(){
                                            if($(this).css('display')=='none'){
                                                $hidden = true;
                                            }
                                        });
                                        var $parent = $element.parents('.super-shortcode:eq(0)');
                                        if( ( $hidden==true )  || ( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) ) {
                                            v.variable_value = v.variable_value.replace('{'+$name+'}', 0);
                                        }else{
                                            if( !$element.length ) {
                                                v.variable_value = v.variable_value.replace('{'+$name+'}', 0);
                                            }else{
                                                var $text_field = true;
                                                var $parent = $element.parents('.super-field:eq(0)');
                                                $value = $element.val();
                                                v.variable_value = v.variable_value.replace('{'+$name+'}', $value);
                                            }
                                        }
                                    }
                                }
                                $field.val(v.variable_value);
                            }else{
                                if($prev_match_found==false){
                                    $field.val('');
                                }
                            }
                        }
                    }
                    $updated_variable_fields[$field.attr('name')] = $field;
                });
            }
        });
        $.each($updated_variable_fields, function( index, field ) {
            SUPER.after_field_change_blur_hook(field);
        });
    }
    SUPER.loop_fade = function($next, $duration){
        $next.fadeIn($duration);  
        if(($next.hasClass('super-extra-shortcode')) || ($next.hasClass('hidden'))){
            SUPER.loop_fade($next.next('.super-field'), $duration);  
        }else{
            var $this = $next.children('div').children('input,textarea,select');
            var $validation = $this.data('validation');
            var $conditional_validation = $this.data('conditional-validation');
            if( ($validation=='none') && ($conditional_validation=='none') ) {
                var $next = $this.parents('.super-field').next('.super-field');
                SUPER.loop_fade($next, $duration);                
            }
        }
    }
    SUPER.complete_submit = function( $form, $duration, $old_html, $status, $status_update ){
        if(typeof $status === 'undefined') var $status = '';
        if(typeof $status_update === 'undefined') var $status_update = '';
        $data = SUPER.prepare_form_data($form);
        var $form_id = $data.form_id;
        var $entry_id = $data.entry_id;
        $data = SUPER.after_form_data_collected_hook($data.data);
        $data['super_hp'] = $form.find('input[name="super_hp"]').val();
        if($data['super_hp']!=''){
            return false;
        }
        var $json_data = JSON.stringify($data);
        $form.find('textarea[name="json_data"]').val($json_data);
        $.ajax({
            url: super_common_i18n.ajaxurl,
            type: 'post',
            data: {
                action: 'super_send_email',
                data: $data,
                form_id: $form_id,
                entry_id: $entry_id,
                entry_status: $status,
                entry_status_update: $status_update
            },
            success: function (result) {
                $('.super-msg').remove();
                var $result = jQuery.parseJSON(result);
                if($result.error==true){
                    var $html = '<div class="super-msg super-error">';
                    if(typeof $result.fields !== 'undefined'){
                        $.each($result.fields, function( index, value ) {
                            $(value+'[name="'+index+'"]').parent().addClass('error');
                        });
                    }                               
                }else{
                    SUPER.after_email_send_hook($form);
                    if( ($form.children('form').attr('method')=='post') && ($form.children('form').attr('action')!='') ){
                        $form.children('form').submit();
                        return false;
                    }
                    var $html = '<div class="super-msg super-success"';
                    if($result.display==false){
                        $html += 'style="display:none;">';
                    }
                    $html += '>';
                }
                if($result.redirect){
                    window.location.href = $result.redirect;
                }else{
                    if($result.msg!=''){
                        $html += $result.msg;
                        $html += '<span class="close"></span>';
                        $html += '</div>';
                        $($html).prependTo($form);
                    }
                    if($result.loading==false){
                        var $proceed = SUPER.before_scrolling_to_message_hook($form, $form.offset().top - 30);
                        if($proceed==true){
                            $('html, body').animate({
                                scrollTop: $form.offset().top-200
                            }, 1000);
                        }
                        $form.find('.super-form-button.super-loading .super-button-name').html($old_html);
                        $form.find('.super-form-button.super-loading').removeClass('super-loading');
                        if($result.error==false){
                            if($form.data('hide')==true){
                                $form.find('.super-field, .super-multipart-progress, .super-field, .super-multipart-steps').fadeOut($duration);
                                setTimeout(function () {
                                    $form.find('.super-field, .super-shortcode').remove();
                                }, $duration);
                            }else{
                                if($form.data('clear')==true){
                                    SUPER.init_clear_form($form);
                                }
                            }
                        }
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('Failed to process data, please try again');
            }
        });
    }
    SUPER.upload_files = function( $form, $data, $duration, $old_html, $status, $status_update ){
        $form.find('.super-fileupload-files').each(function(){
            var $minfiles = $(this).parent().find('.super-selected-files').data('minfiles');
            if( typeof $minfiles === 'undefined' ) {
                $minfiles = 0;
            }
            if( ( $minfiles==0 ) && ( $(this).parent().find('.super-fileupload-files').children('div').length == 0 ) ) {
                $(this).parent().find('.super-fileupload').addClass('finished');
            }
        });
        $form.find('.super-fileupload-files > div:not(.super-uploaded)').each(function(){
            var data = $(this).data();
            data.submit();
        });
        $form.find('.super-fileupload').on('fileuploaddone', function (e, data) {
            var $field = $(this).parents('.super-field-wrapper:eq(0)').children('input[type="hidden"]');
            $.each(data.result.files, function (index, file) {
                if(!file.error){
                    if($field.val()==''){
                        $field.val(file.name);
                    }else{
                        $field.val($field.val()+','+file.name);
                    }
                }
            });
            var $value = $field.val();
            var $value = $value.split(',');
            $data[$field.attr('name')] = $field.val();
            if($(this).parents('.super-field-wrapper:eq(0)').find('.super-fileupload-files > div.error').length){
                $form.find('.super-form-button.super-loading .super-button-name').html($old_html);
                $form.find('.super-form-button.super-loading').removeClass('super-loading');
                clearInterval($interval);
            }else{
                if($(this).parents('.super-field-wrapper:eq(0)').find('.super-fileupload-files > div:not(.error)').length == $value.length){
                    $(this).addClass('finished');
                }
            }
        });
        var $interval = setInterval(function() {
            var $total_file_uploads = 0;
            $form.find('.super-fileupload').each(function(){
                var $shortcode_field = $(this);
                var $skip = false;
                $shortcode_field.parents('.super-shortcode.super-column').each(function(){
                    if($(this).css('display')=='none') {
                        $skip = true;
                    }
                });
                var $parent = $shortcode_field.parents('.super-shortcode:eq(0)');
                if( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) {
                    $skip = true;
                }
                if( $skip!=true ) {
                    $total_file_uploads++;
                }else{
                    $shortcode_field.removeClass('finished');
                }
            });
            if($form.find('.super-fileupload.finished').length == $total_file_uploads){
                clearInterval($interval);
                SUPER.init_fileupload_fields();
                $form.find('.super-fileupload').removeClass('rendered').fileupload('destroy');
                setTimeout(function() {
                    SUPER.complete_submit( $form, $duration, $old_html, $status, $status_update );
                }, 1000);
            }
        }, 1000);
    }
    SUPER.trim = function($this) {
        if(typeof $this === 'string'){
            return $this.replace(/^\s+|\s+$|\s+(?=\s)/g, "");
        }
    }
    SUPER.handle_validations = function($this, $validation, $conditional_validation, $duration) {
        /*
        For validations we can set a lot of options.
        However, we only used the most commonly used ones.
        Below is a complete list for all possible validation to use in futurue
        */
        /*--- jQuery RegExp for Numbers ---*/
        var intRegex = '/[0-9 -()+]+$/';   
        var ipRegex = 'bd{1,3}.d{1,3}.d{1,3}.d{1,3}b';  
        var num0to255Regex = '^([01][0-9][0-9]|2[0-4][0-9]|25[0-5])$';
        var num0to999Regex = '^([0-9]|[1-9][0-9]|[1-9][0-9][0-9])$';
        var floatRegex = '[-+]?([0-9]*.[0-9]+|[0-9]+)'; 
        var number1to50Regex = '/(^[1-9]{1}$|^[1-4]{1}[0-9]{1}$|^50$)/gm'; 
        /*--- jQuery RegExp for Validation ---*/
        var emailRegex = '^[A-Z0-9._%+-]+@[A-Z0-9.-]+.[A-Z]{2,4}$'; 
        var creditCardRegex = '^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35d{3})d{11})$'; 
        var usernameRegex = '/^[a-z0-9_-]{3,16}$/'; 
        var passwordRegex = '/^[a-z0-9_-]{6,18}$/'; 
        var passwordStrengthRegex = '/((?=.*d)(?=.*[a-z])(?=.*[A-Z]).{8,15})/gm'; 
        var phoneNumber = '/[0-9-()+]{3,20}/'; 
        /*--- jQuery RegExp for Dates ---*/
        var dateRegex = '/(d{1,2}/d{1,2}/d{4})/gm'; 
        var dateMMDDYYYRegex = '^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)dd$'; 
        var dateDDMMYYYRegex = '^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)dd$';
        /*--- jQuery RegExp for URLs ---*/
        var urlslugRegex = '/^[a-z0-9-]+$/'; 
        var urlRegex = /^(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9]+([\-\.]{1}[a-zA-Z0-9]+)*\.[a-zA-Z]{2,5}(:[0-9]{1,5})?(\/.*)?$/;
        /*--- jQuery RegExp for Domain Names ---*/
        var domainRegex = '/(.*?)[^w{3}.]([a-zA-Z0-9]([a-zA-Z0-9-]{0,65}[a-zA-Z0-9])?.)+[a-zA-Z]{2,6}/igm'; 
        var domainRegex = '/[^w{3}.]([a-zA-Z0-9]([a-zA-Z0-9-]{0,65}[a-zA-Z0-9])?.)+[a-zA-Z]{2,6}/igm'; 
        var domainRegex = '/(.*?).(com|net|org|info|coop|int|com.au|co.uk|org.uk|ac.uk|)/igm'; 
        var subDomainRegex = '/(http://|https://)?(www.|dev.)?(int.|stage.)?(travel.)?(.*)+?/igm';
        /*--- jQuery RegExp for Images ---*/
        var imageRegex = '/([^s]+(?=.(jpg|gif|png)).2)/gm'; 
        var imgTagsRegex = '/<img .+?src="(.*?)".+?/>/ig';  
        var imgPngRegex = '/<img .+?src="(.*?.png)".+?/>/ig';
        /*--- Other Useful jQuery RegExp Examples ---*/
        var rgbRegex = '/^rgb((d+),s*(d+),s*(d+))$/';  
        var hexRegex = '/^#?([a-f0-9]{6}|[a-f0-9]{3})$/'; 
        var hexRegex = '/(#?([A-Fa-f0-9]){3}(([A-Fa-f0-9]){3})?)/gm'; 
        var htmlTagRegex = '/^< ([a-z]+)([^<]+)*(?:>(.*)< /1>|s+/>)$/'; 
        var htmlTagRegex = '/(< (/?[^>]+)>)/gm'; 
        var productUrlRegex = '(/product/)?+[0-9]+';  
        var lnhRegex = '/([A-Za-z0-9-]+)/gm';  
        var jsTagsRegex = '/<script .+?src="(.+?.js(?:?v=d)*).+?script>/ig';  
        var cssTagsRegex = '/<link .+?href="(.+?.css(?:?v=d)*).+?/>/ig'; 
        var $error = false;
        var $custom_regex = $this.parent().find('.super-custom-regex').val();
        var $may_be_empty = $this.data('may-be-empty');
        if( ($may_be_empty==true) && ($this.val().length==0) ) {
            return false;
        }
        $('.super-field.conditional[data-conditionalfield="'+$this.attr('name')+'"]').each(function(){
            if($(this).data('conditionalvalue')==$this.val()){
                $(this).addClass('active');
                $(this).find('select').data('excludeconditional','0');
            }else{
                $(this).removeClass('active');
                $(this).find('select').data('excludeconditional','1');
            }
        });
        if( $custom_regex!='' ) {
            var $regex = new RegExp($custom_regex);
            var $value = $this.val();
            if($regex.test($value)) {
            }else{
                $error = true;
            }
        }
        if ($validation == 'captcha') {
            $error = true;
        }
        if ($validation == 'numeric') {
            var $regex = /^\d+$/;
            var $value = $this.val();
            if (!$regex.test($value)) {
                $error = true;
            }
        }
        if ($validation == 'float') {
            var $regex = /^[+-]?\d+(\.\d+)?$/;
            var $value = $this.val();
            if (!$regex.test($value)) {
                $error = true;
            }
        }
        if ($validation == 'empty') {
            if(SUPER.trim($this.val()) == '') {
                $error = true;
            }
        }
        if ($validation == 'email') {
            if (($this.val().length < 4) || (!/^([\w-\.]+@([\w-]+\.)+[\w-]{2,63})?$/.test($this.val()))) {
                $error = true;
            }
        }
        if ($validation == 'phone') {
            var $regex = /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/;
            var $value = $this.val();
            var $numbers = $value.split("").length;
            if (10 <= $numbers && $numbers <= 20 && $regex.test($value)) {
            }else{
                $error = true;
            }
        }
        if ($validation == 'website') {
            var $value = $this.val();
            var pattern = new RegExp(urlRegex);
            if(pattern.test($value)) {
            }else{
                $error = true;
            }
        }
        if ($validation == 'iban') {
            var $value = $this.val();
            if( (IBAN.isValid($value)==false) && ($value!='') ) {
                $error = true;
            }
        }
        var $attr = $this.attr('data-minlength');
        if (typeof $attr !== 'undefined' && $attr !== false) {
            var $text_field = true;
            var $total = 0;
            var $parent = $this.parents('.super-field:eq(0)');
            if($parent.hasClass('super-checkbox')){
                $text_field = false;
                var $checked = $parent.find('label.super-selected');
                if($checked.length < $attr){
                    $error = true;
                }
            }
            if( ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ){
                $text_field = false;
                var $total = $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)').length;
                if($total < $attr){
                    $error = true;
                }
            }
            if($text_field==true){
                if(!$parent.hasClass('super-date')){
                    if($this.val().length < $attr){
                        $error = true;
                    }
                }
            }       
        }
        var $attr = $this.attr('data-maxlength');
        if (typeof $attr !== 'undefined' && $attr !== false) {
            var $text_field = true;
            var $total = 0;
            var $parent = $this.parents('.super-field:eq(0)');
            if($parent.hasClass('super-checkbox')){
                $text_field = false;
                var $checked = $parent.find('label.super-selected');
                if($checked.length > $attr){
                    $error = true;
                }
            }
            if( ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ){
                $text_field = false;
                var $total = $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)').length;
                if($total > $attr){
                    $error = true;
                }
            }
            if($text_field==true){
                if(!$parent.hasClass('super-date')){
                    if($this.val().length > $attr){
                        $error = true;
                    }
                }
            }
        }
        var $attr = $this.attr('data-minnumber');
        if (typeof $attr !== 'undefined' && $attr !== false) {
            var $parent = $this.parents('.super-field:eq(0)');
            if($parent.hasClass('super-currency')){
                var $value = $this.val();
                var $currency = $this.data('currency');
                var $format = $this.data('format');
                var $decimals = $this.data('decimals');
                var $thousand_separator = $this.data('thousand-separator');
                var $decimal_seperator = $this.data('decimal-separator');
                $value = $value.replace($currency, '').replace($format, '');
                $value = $value.split($thousand_separator).join('');
                $value = $value.split($decimal_seperator).join('.');
                $value = ($value) ? parseFloat($value) : 0;
                if( $value < parseFloat($attr) ) {
                    $error = true;
                }
            }else{
                if( parseFloat($this.val()) < parseFloat($attr) ) {
                    $error = true;
                }
            }
        }
        var $attr = $this.attr('data-maxnumber');
        if (typeof $attr !== 'undefined' && $attr !== false) {
            var $parent = $this.parents('.super-field:eq(0)');
            if($parent.hasClass('super-currency')){
                var $value = $this.val();
                var $currency = $this.data('currency');
                var $format = $this.data('format');
                var $decimals = $this.data('decimals');
                var $thousand_separator = $this.data('thousand-separator');
                var $decimal_seperator = $this.data('decimal-separator');
                $value = $value.replace($currency, '').replace($format, '');
                $value = $value.split($thousand_separator).join('');
                $value = $value.split($decimal_seperator).join('.');
                $value = ($value) ? parseFloat($value) : 0;
                if( $value > parseFloat($attr) ) {
                    $error = true;
                }
            }else{
                if( parseFloat($this.val()) > parseFloat($attr) ) {
                    $error = true;
                }
            }
        }    
        var $logic = $conditional_validation;
        if( typeof $logic !== 'undefined' && $logic!='none' && $logic!='' ) {
            var $field_value = $this.val();
            var $parent = $this.parents('.super-field:eq(0)');
            if($parent.hasClass('super-currency')){
                var $value = $this.val();
                var $currency = $this.data('currency');
                var $format = $this.data('format');
                var $decimals = $this.data('decimals');
                var $thousand_separator = $this.data('thousand-separator');
                var $decimal_seperator = $this.data('decimal-separator');
                $value = $value.replace($currency, '').replace($format, '');
                $value = $value.split($thousand_separator).join('');
                $value = $value.split($decimal_seperator).join('.');
                $field_value = ($value) ? parseFloat($value) : 0;
            }
            var $value = $this.data('conditional-validation-value');
            var $value2 = $this.data('conditional-validation-value2');
            if(typeof $value !== 'undefined'){
                var $string_value = $value.toString();
                var $string_field_value = $field_value.toString();
                var $bracket = "{";
                if($string_value.indexOf($bracket) != -1){
                    var $form = $this.parents('.super-form:eq(0)');
                    var $regular_expression = /\{(.*?)\}/g;
                    var $name = $regular_expression.exec($value);
                    var $name = $name[1];
                    var $element = $form.find('.super-shortcode-field[name="'+$name+'"]');
                    if($element.length){
                        var $text_field = true;
                        var $parent = $element.parents('.super-field:eq(0)');
                        if( ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ){
                            $text_field = false;
                            var $sum = 0;
                            var $selected = $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)');
                            $selected.each(function () {
                                $sum += $(this).data('value');
                            });
                            $value = $sum;
                        }
                        if($parent.hasClass('super-checkbox')){
                            $text_field = false;
                            var $sum = 0;
                            var $checked = $parent.find('input[type="checkbox"]:checked');
                            $checked.each(function () {
                                $sum += $(this).val();
                            });
                            $value = $sum;
                        }
                        if($parent.hasClass('super-currency')){
                            $text_field = false;
                            var $value = $element.val();
                            var $currency = $element.data('currency');
                            var $format = $element.data('format');
                            var $decimals = $element.data('decimals');
                            var $thousand_separator = $element.data('thousand-separator');
                            var $decimal_seperator = $element.data('decimal-separator');
                            $value = $value.replace($currency, '').replace($format, '');
                            $value = $value.split($thousand_separator).join('');
                            $value = $value.split($decimal_seperator).join('.');
                            $value = ($value) ? parseFloat($value) : 0;
                        }
                        if($text_field==true){
                            $value = ($element.val()) ? $element.val() : '';
                        }
                    }
                }
            }
            if(typeof $value2 !== 'undefined'){
                var $string_value = $value2.toString();
                var $string_field_value = $field_value.toString();
                var $bracket = "{";
                if($string_value.indexOf($bracket) != -1){
                    var $form = $this.parents('.super-form:eq(0)');
                    var $regular_expression = /\{(.*?)\}/g;
                    var $name = $regular_expression.exec($value2);
                    var $name = $name[1];
                    var $element = $form.find('.super-shortcode-field[name="'+$name+'"]');
                    if($element.length){
                        var $text_field = true;
                        var $parent = $element.parents('.super-field:eq(0)');
                        if( ($parent.hasClass('super-dropdown')) || ($parent.hasClass('super-countries')) ){
                            $text_field = false;
                            var $sum = 0;
                            var $selected = $parent.find('.super-dropdown-ui li.selected:not(.super-placeholder)');
                            $selected.each(function () {
                                $sum += $(this).data('value');
                            });
                            $value2 = $sum;
                        }
                        if($parent.hasClass('super-checkbox')){
                            $text_field = false;
                            var $sum = 0;
                            var $checked = $parent.find('input[type="checkbox"]:checked');
                            $checked.each(function () {
                                $sum += $(this).val();
                            });
                            $value2 = $sum;
                        }
                        if($parent.hasClass('super-currency')){
                            $text_field = false;
                            var $value2 = $element.val();
                            var $currency = $element.data('currency');
                            var $format = $element.data('format');
                            var $decimals = $element.data('decimals');
                            var $thousand_separator = $element.data('thousand-separator');
                            var $decimal_seperator = $element.data('decimal-separator');
                            $value2 = $value2.replace($currency, '').replace($format, '');
                            $value2 = $value2.split($thousand_separator).join('');
                            $value2 = $value2.split($decimal_seperator).join('.');
                            $value2 = ($value2) ? parseFloat($value2) : 0;
                        }
                        if($text_field==true){
                            $value2 = ($element.val()) ? $element.val() : '';
                        }
                    }
                }
            }
            var $counter = 0;
            if($logic=='equal'){
                if($field_value==$value){
                    $counter++;
                }                            
            }
            if($logic=='not_equal'){
                if($field_value!=$value){
                    $counter++;
                }                            
            }
            if($logic=='contains'){
                if($field_value.indexOf($value) >= 0){
                    $counter++;
                }
            }
            $field_value = parseFloat($field_value);
            $value = parseFloat($value);
            $value2 = parseFloat($value2);
            if($logic=='greater_than'){
                if($field_value>$value){
                    $counter++;
                }                            
            }
            if($logic=='less_than'){
                if($field_value<$value){
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_equal'){
                if($field_value>=$value){
                    $counter++;
                }                            
            }
            if($logic=='less_than_or_equal'){
                if($field_value<=$value){
                    $counter++;
                }                            
            }
            if($logic=='greater_than_and_less_than'){
                if( ($field_value>$value) && ($field_value<$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_less_than'){
                if( ($field_value>$value) || ($field_value<$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_equal_and_less_than'){
                if( ($field_value>=$value) && ($field_value<$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_equal_or_less_than'){
                if( ($field_value>=$value) || ($field_value<$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_and_less_than_or_equal'){
                if( ($field_value>$value) && ($field_value<=$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_less_than_or_equal'){
                if( ($field_value>$value) || ($field_value<=$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_equal_and_less_than_or_equal'){
                if( ($field_value>=$value) && ($field_value<=$value2) ) {
                    $counter++;
                }                            
            }
            if($logic=='greater_than_or_equal_or_less_than_or_equal'){
                if( ($field_value>=$value) || ($field_value<=$value2) ) {
                    $counter++;
                }                            
            }
            if($counter==0){
                $error = true;
            }
        }
        if($this.hasClass('super-fileupload')){
            var $file_error = false;
            var $attr = $this.parent().find('.super-selected-files').data('minfiles');
            if (typeof $attr !== 'undefined' && $attr !== false) {
                var $total = $this.parent().find('.super-fileupload-files').children('div').length;
                if($total < $attr) {
                    $error = true;
                }
            }
            var $attr = $this.parent().find('.super-selected-files').data('maxfiles');
            if (typeof $attr !== 'undefined' && $attr !== false) {
                var $total = $this.parent().find('.super-fileupload-files').children('div').length;
                if($total > $attr) {
                    $error = true;
                }
            }
        }
        if($error==true){
            SUPER.handle_errors($this, $duration);
            var $index = $this.parents('.super-multipart:eq(0)').index('.super-form:eq(0) .super-multipart');
            $this.parents('.super-form:eq(0)').find('.super-multipart-steps').children('.super-multipart-step:eq('+$index+')').addClass('super-error');
        }else{
            $this.parents('.super-field:eq(0)').removeClass('error-active');
            $this.parents('.super-field:eq(0)').children('p').fadeOut($duration, function() {
                $(this).remove();
            });
        }
        if($this.parents('.super-multipart:eq(0)').find('.super-field > p').length==0){
            var $index = $this.parents('.super-multipart:eq(0)').index('.super-form:eq(0) .super-multipart');
            $this.parents('.super-form:eq(0)').find('.super-multipart-steps').children('.super-multipart-step:eq('+$index+')').removeClass('super-error');
        }
        return $error;
    }
    SUPER.custom_theme_error = function($form, $this){
        if($form.find('input[name="hidden_theme"]').length != 0){
            var $theme_options = $form.find('input[name="hidden_theme"]').data();
            $this.attr('style', 'background-color:'+$theme_options['error_bg']+';border-color:'+$theme_options['error_border']+';color:'+$theme_options['error_font']);
        }        
    }
    SUPER.get_duration = function($form){
        if($form.find('input[name="hidden_duration"]').length == 0){
            var $duration = parseFloat(super_common_i18n.duration);
        }else{
            var $duration = parseFloat($form.find('input[name="hidden_duration"]').val());
        }
        return $duration;
    }
    SUPER.handle_errors = function($this, $duration){
        var $error_position = $this.parents('.super-field:eq(0)');
        var $position = 'after';
        if(($error_position.hasClass('top-left')) || ($error_position.hasClass('top-right'))){
            var $position = 'before';
        }
        if ($this.data('message')){
            var $message = $this.data('message');
        }else{
            var $message = super_common_i18n.errors.fields.required;
        }
        if ($this.parents('.super-field:eq(0)').children('p').length == 0) {
            var $element = $this.parents('.super-field-wrapper:eq(0)');
            if($this.hasClass('super-recaptcha')){
                var $element = $this;
            }
            if($position=='before'){
                $('<p style="display:none;">' + $message + '</p>').insertBefore($element);
            }
            if($position=='after'){
                $('<p style="display:none;">' + $message + '</p>').appendTo($element.parents('.super-field:eq(0)'));
            }
        }
        if(($this.parents('.super-field').next('.grouped').length != 0) || ($this.parents('.super-field').hasClass('grouped'))){
            $this.parent().children('p').css('max-width', $this.parent().outerWidth()+'px');
        }
        SUPER.custom_theme_error($this.parents('.super-form'), $this.parent().children('p'));
        $this.parents('.super-field:eq(0)').addClass('error-active');
        $this.parents('.super-field:eq(0)').children('p').fadeIn($duration);
    }
    SUPER.validate_form = function( $form, $submit_button, $validate_multipart, e ) {
        var $action = $submit_button.children('.super-button-name').data('action');
        if($action=='clear'){
            SUPER.init_clear_form($form);
            return false;
        }
        if($action=='print'){
            SUPER.init_print_form($form, $submit_button);
            return false;
        }
        var $url = $submit_button.data('href');
        var $proceed = SUPER.before_submit_button_click_hook(e, $submit_button);
        if($proceed==true){
            if( ($url!='') && (typeof $url !== 'undefined') ){
                var $regular_expression = /\{(.*?)\}/g;
                var $array = [];
                var $i = 0;
                while (($match = $regular_expression.exec($url)) != null) {
                    $array[$i] = $match[1];
                    $i++;
                }
                for (var $i = 0; $i < $array.length; $i++) {
                    var $name = $array[$i];
                    var $element = $form.find('.super-shortcode-field[name="'+$name+'"]');
                    if($element.length){
                        $value = $element.val();
                        $url = $url.replace('{'+$name+'}', $value);
                    }
                }
                $url = $url.replace('{', '').replace('}', '');
                if( $url=='#' ) {
                    return false;
                }else{
                    var $target = $submit_button.data('target');
                    if( ($target!=='undefined') && ($target=='_blank') ) {
                        window.open( $url, '_blank' );
                    }else{
                        window.location.href = $url;
                    }
                    return false;
                }
            }else{
                if($submit_button.parent('.super-form-button').hasClass('super-loading')){
                    return false;
                }
            }
        }
        if(typeof $validate_multipart === 'undefined') $validate_multipart = '';
        SUPER.before_validating_form_hook(undefined, $form);
        var $data = [],
            $error = false;
        var $duration = SUPER.get_duration($form);
        if( typeof tinyMCE !== 'undefined' ) {
            if( typeof tinyMCE.triggerSave !== 'undefined' ) {
                tinyMCE.triggerSave();
            }
        }
        $form.find('.super-field').find('.super-shortcode-field, .super-recaptcha, .super-selected-files').each(function () {
            var $hidden = false;
            var $this = $(this);
            $this.parents('.super-shortcode.super-column').each(function(){
                if($(this).css('display')=='none'){
                    $hidden = true;
                }
            });
            var $parent = $this.parents('.super-shortcode:eq(0)');
            if( ( $hidden==true )  || ( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) ) {
            }else{
                var $text_field = true;
                if($this.hasClass('super-recaptcha')){
                    $text_field = false;
                    if($this.data('verified')!=1){
                        if (SUPER.handle_validations($this, 'captcha', '', $duration)) {
                            $error = true;
                        }
                    }
                }
                if($this.hasClass('super-selected-files')){
                    $text_field = false;
                    var $file_error = false;
                    var $attr = $this.data('minfiles');
                    if (typeof $attr !== 'undefined' && $attr !== false) {
                        var $total = $this.parent().find('.super-fileupload-files').children('div').length;
                        if($total < $attr) {
                            $file_error = true;
                        }
                    }
                    var $attr = $this.data('maxfiles');
                    if (typeof $attr !== 'undefined' && $attr !== false) {
                        var $total = $this.parent().find('.super-fileupload-files').children('div').length;
                        if($total > $attr) {
                            $file_error = true;
                        }
                    }
                    if($file_error==true){
                        $error = true;
                        SUPER.handle_errors($this, $duration);
                        var $index = $this.parents('.super-multipart:eq(0)').index('.super-form:eq(0) .super-multipart');
                        $this.parents('.super-form:eq(0)').find('.super-multipart-steps').children('.super-multipart-step:eq('+$index+')').addClass('super-error');
                    }else{
                        $this.parents('.super-field:eq(0)').removeClass('error-active');
                        $this.parents('.super-field:eq(0)').children('p').fadeOut($duration, function() {
                            $(this).remove();
                        });
                    }
                    if($this.parents('.super-multipart:eq(0)').find('.super-field > p').length==0){
                        var $index = $this.parents('.super-multipart:eq(0)').index('.super-form:eq(0) .super-multipart');
                        $this.parents('.super-form:eq(0)').find('.super-multipart-steps').children('.super-multipart-step:eq('+$index+')').removeClass('super-error');
                    }
                }
                if($text_field==true){
                    var $validation = $this.data('validation');
                    var $conditional_validation = $this.data('conditional-validation');
                    if (SUPER.handle_validations($this, $validation, $conditional_validation, $duration)) {
                        $error = true;
                    }
                }
            }
        });
        if($error==false){  
            if($validate_multipart==true) return true;
            var $submit_button_name = $submit_button.children('.super-button-name');
            $submit_button.parents('.super-form-button:eq(0)').addClass('super-loading');
            var $old_html = $submit_button_name.html();
            var $loading = $submit_button.children('.super-button-name').data('loading');
            if(super_common_i18n.loading!='Loading...') {
                $loading = super_common_i18n.loading;
            }
            var $status = $submit_button_name.data('status');
            var $status_update = $submit_button_name.data('status-update');
            $submit_button_name.html('<i class="fa fa-refresh fa-spin"></i>'+$loading);
            if ($form.find('.super-fileupload-files > div').length != 0) {
                SUPER.upload_files( $form, $data, $duration, $old_html, $status, $status_update );
            }else{
                SUPER.complete_submit( $form, $duration, $old_html, $status, $status_update );
            }
        }else{
            if($validate_multipart==true) {
                var $scroll = true;
                if(typeof $form.attr('data-disable-scroll') !== 'undefined'){
                    $scroll = false;
                }
                if($scroll){
                    $('html, body').animate({
                        scrollTop: $form.parents('.super-form:eq(0)').offset().top-30
                    }, 1000);
                }
                return false;
            }
            if($form.find('.super-multipart-step.super-error').length){
                var $this = $form.find('.super-multipart-step.super-error:eq(0)');
                var $index = $this.index();
                var $total = $form.find('.super-multipart').length;
                var $progress = 100 / $total;
                var $progress = $progress * ($index+1);
                var $multipart = $form.find('.super-multipart:eq('+$index+')');
                var $scroll = true;
                if(typeof $multipart.attr('data-disable-scroll') !== 'undefined'){
                    $scroll = false;
                }
                $form.find('.super-multipart-progress-bar').css('width',$progress+'%');
                $form.find('.super-multipart-step').removeClass('active');
                $form.find('.super-multipart').removeClass('active');
                $multipart.addClass('active');
                $this.addClass('active');
                var $proceed = SUPER.before_scrolling_to_error_hook($form, $form.offset().top - 30);
                if($proceed!=true) return false;
                if($scroll){
                    $('html, body').animate({
                        scrollTop: $this.parents('.super-form:eq(0)').offset().top - 30 
                    }, 1000);
                }
            }else{
                var $proceed = SUPER.before_scrolling_to_error_hook($form, $form.find('.super-field > p').offset().top-200);
                if($proceed!=true) return false;
                $('html, body').animate({
                    scrollTop: $form.find('.super-field > p').offset().top-200
                }, 1000);
            }
        }
        SUPER.after_validating_form_hook(undefined, $form);
    }
    SUPER.auto_step_multipart = function($field){
        var $form = $field.parents('.super-form:eq(0)');
        var $active_part = $form.find('.super-multipart.active');
        var $auto_step = $active_part.data('step-auto');
        if( $auto_step=='yes') {
            var $total_fields = 0;
            $active_part.find('.super-shortcode-field').each(function(){
                var $this = $(this);
                var $hidden = false;
                $this.parents('.super-shortcode.super-column').each(function(){
                    if($(this).css('display')=='none'){
                        $hidden = true;
                    }
                });
                var $parent = $this.parents('.super-shortcode:eq(0)');
                if( ($hidden==true)  || ($parent.css('display')=='none') ) {
                }else{
                    $total_fields++;
                }
            });
            var $counter = 1;
            $active_part.find('.super-shortcode-field').each(function(){
                var $this = $(this);
                var $hidden = false;
                $this.parents('.super-shortcode.super-column').each(function(){
                    if($(this).css('display')=='none'){
                        $hidden = true;
                    }
                });
                var $parent = $this.parents('.super-shortcode:eq(0)');
                if( ($hidden==true)  || ($parent.css('display')=='none') ) {
                }else{
                    if($total_fields==$counter){
                        if($this.attr('name')==$field.attr('name')){
                            setTimeout(function (){
                                $active_part.find('.super-next-multipart').click();
                            }, 200);
                        }
                    }
                    $counter++;
                }
            });
        }
    }
    SUPER.before_validating_form_hook = function($changed_field, $form){
        var $functions = super_common_i18n.dynamic_functions.before_validating_form_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($changed_field, $form);
            }
        });
    }
    SUPER.after_validating_form_hook = function($changed_field, $form){
        var $functions = super_common_i18n.dynamic_functions.after_validating_form_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($changed_field, $form);
            }
        });
    }
    SUPER.after_initializing_forms_hook = function($changed_field, $form){
        var $functions = super_common_i18n.dynamic_functions.after_initializing_forms_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($changed_field, $form);
            }
        });
    }
    SUPER.get_frontend_or_backend_form = function(){
        if($('.super-live-preview').length) {
            return $('.super-live-preview');
        }else{
            return $(document);
        }
    }
    SUPER.after_dropdown_change_hook = function($field){
        if( typeof $field !== 'undefined' ) {
            SUPER.auto_step_multipart($field);
            var $form = $field.parents('.super-form:eq(0)');
        }else{
            var $form = SUPER.get_frontend_or_backend_form();
        }
        var $functions = super_common_i18n.dynamic_functions.after_dropdown_change_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($field, $form);
            }
        });
        SUPER.save_form_progress($form); // @since 3.2.0
    }
    SUPER.after_field_change_blur_hook = function($field, $form, $skip){
        if( (typeof $field !== 'undefined') && ($skip!=false) ) {
            var $form = $field.parents('.super-form:eq(0)');
        }else{
            var $form = SUPER.get_frontend_or_backend_form();
        }
        var $functions = super_common_i18n.dynamic_functions.after_field_change_blur_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($field, $form, $skip);
            }
        });
        SUPER.save_form_progress($form);
    }
    SUPER.after_radio_change_hook = function($field){
        if( typeof $field !== 'undefined' ) {
            SUPER.auto_step_multipart($field);
            var $form = $field.parents('.super-form:eq(0)');
        }else{
            var $form = SUPER.get_frontend_or_backend_form();      
        }
        var $functions = super_common_i18n.dynamic_functions.after_radio_change_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($field, $form);
            }
        });
        SUPER.save_form_progress($form); // @since 3.2.0
    }
    SUPER.after_checkbox_change_hook = function($field){
        if( typeof $field !== 'undefined' ) {
            SUPER.auto_step_multipart($field);
            var $form = $field.parents('.super-form:eq(0)');
        }else{
            var $form = SUPER.get_frontend_or_backend_form();
        }
        var $functions = super_common_i18n.dynamic_functions.after_checkbox_change_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($field, $form);
            }
        });
        SUPER.save_form_progress($form); // @since 3.2.0
    }
    SUPER.save_form_progress_timeout = null; 
    SUPER.save_form_progress = function($form){
        if( !$form.hasClass('super-save-progress') ) {
            return false;
        }
        if(SUPER.save_form_progress_timeout !== null){
            clearTimeout(SUPER.save_form_progress_timeout);
        }
        SUPER.save_form_progress_timeout = setTimeout(function () {
            var $data = SUPER.prepare_form_data($form);
            var $form_id = $data.form_id;
            var $data = SUPER.after_form_data_collected_hook($data.data);
            $.ajax({
                url: super_common_i18n.ajaxurl,
                type: 'post',
                data: {
                    action: 'super_save_form_progress',
                    data: $data,
                    form_id: $form_id
                }
            });
        }, 300);
    }
    SUPER.after_email_send_hook = function($form){
        var ga = window[window['GoogleAnalyticsObject'] || 'ga'];
        if (typeof ga == 'function') {
            var $ga_tracking = super_common_i18n.ga_tracking;
            var $ga_tracking = $ga_tracking.split('\n');
            $($ga_tracking).each(function(index, value){
                var $proceed = true;
                var $values = value.split(":");
                if($values.length>1){
                    var $event = $values[1].split("|");
                    if(!$form.hasClass('super-form-'+$values[0])){
                        $proceed = false;
                    }
                }else{
                    var $event = $values[0].split("|");
                }
                if($proceed){
                    if( ( (typeof $event[1] === 'undefined') || ($event[1]=='') ) || 
                        ( (typeof $event[2] === 'undefined') || ($event[2]=='') ) ) {
                        console.log('Seems like we are missing required ga() parameters!');
                    }else{
                        if( ($event[0]=='send') && ($event[1]=='event') ) {
                            if( (typeof $event[3] === 'undefined') || ($event[3]=='') ) {
                                console.log('ga() is missing the "eventAction" parameter (The type of interaction e.g. "play")');
                            }else{
                                var $parameters = {};
                                $parameters.hitType = $event[1];
                                $parameters.eventCategory = $event[2];
                                $parameters.eventAction = $event[3];
                                if( typeof $event[4] !== 'undefined' ) {
                                    $parameters.eventLabel = $event[4];
                                }
                                if( typeof $event[5] !== 'undefined' ) {
                                    $parameters.eventValue = $event[5];
                                }
                                ga($event[0], $parameters);
                            }
                        }
                        /* (the following might be usefull for near future?)
                        if( ($event[0]=='send') && ($event[1]=='pageview') ) {
                            ga($event);
                        }
                        if( ($event[0]=='send') && ($event[1]=='social') ) {
                            if( ( (typeof $event[3] === 'undefined') || ($event[3]=='') ) || 
                                ( (typeof $event[4] === 'undefined') || ($event[4]=='') ) ) {
                                console.log('ga() is missing the "socialAction" parameter (The type of action that happens e.g. "Like", "Send", "Tweet".)');
                                console.log('ga() is missing the "socialTarget" parameter (Specifies the target of a social interaction. This value is typically a URL but can be any text. e.g. "http://mycoolpage.com")');
                            }else{
                                ga($event);
                            }
                        }
                        */
                    }
                }
            });
        }else{
            console.log('Could not submit tracking event because ga() is not a function. This means the analytics.js library is not loaded correctly.');
        }
        /*
        ga('send', {
          hitType: 'event',
          eventCategory: 'Signup Form',
          eventAction: 'submit',
          eventLabel: 'Fall Campaign'
        });
        /*
        ga('send', {
          hitType: 'event',
          eventCategory: 'Signup Form',
          eventAction: 'submit',
          eventLabel: 'Fall Campaign'
        });
        */
        var $functions = super_common_i18n.dynamic_functions.after_email_send_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($form);
            }
        });    
    }
    SUPER.after_responsive_form_hook = function($classes, $new_class, $window_classes, $new_window_class){
        var $functions = super_common_i18n.dynamic_functions.after_responsive_form_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($classes, $new_class, $window_classes, $new_window_class);
            }
        });    
    }
    SUPER.prepare_form_data = function($form){
        var $data = {};
        var $form_id = '';
        $form.find('.super-shortcode-field').each(function(){
            var $this = $(this);
            var $hidden = false;
            $this.parents('.super-shortcode.super-column').each(function(){
                if($(this).css('display')=='none'){
                    $hidden = true;
                }
            });
            var $parent = $this.parents('.super-shortcode:eq(0)');
            if( ( $hidden==true )  || ( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) ) {
            }else{
                if($this.hasClass('super-fileupload')){
                    var $parent = $this.parents('.super-field-wrapper:eq(0)');
                    var $field = $parent.find('.super-selected-files');                
                    var $files = $parent.find('.super-fileupload-files > div');
                    $data[$field.attr('name')] = {
                        'label':$field.data('email'),
                        'type':'files',
                        'exclude':$field.data('exclude'),
                        'exclude_entry':$field.data('exclude-entry'),
                        'files':{}};
                    $files.each(function(index,file){
                        var file = $(this);
                        $data[$field.attr('name')]['files'][index] = { 
                            'name':$field.attr('name'),
                            'value':file.attr('data-name'),
                            'url':file.attr('data-url'),
                            'thumburl':file.attr('data-thumburl'),
                            'label':$field.data('email'),
                            'exclude':$field.data('exclude'),
                            'exclude_entry':$field.data('exclude-entry'),
                            'excludeconditional':$field.data('excludeconditional'),
                        };
                    });
                }else{
                    $data[$this.attr('name')] = { 
                        'name':$this.attr('name'),
                        'value':$this.val(),
                        'label':$this.data('email'),
                        'exclude':$this.data('exclude'),
                        'replace_commas':$this.data('replace-commas'),
                        'exclude_entry':$this.data('exclude-entry'),
                        'excludeconditional':$this.data('excludeconditional'),
                        'type':'var'
                    };
                    var $super_field = $this.parents('.super-field:eq(0)');
                    if($super_field.hasClass('super-textarea')){
                        $data[$this.attr('name')]['type'] = 'text';
                    }
                    if($this.hasClass('super-address-autopopulate')){
                        $data[$this.attr('name')]['type'] = 'google_address';
                        $data[$this.attr('name')]['geometry'] = {
                            location: {
                                'lat':$this.data('lat'),
                                'lng':$this.data('lng'),
                            }
                        }
                    }
                    if($super_field.hasClass('super-hidden')){
                        if($this.data('code')==true) {
                            $data[$this.attr('name')]['code'] = 'true';
                            if($this.attr('data-invoice-padding')){
                                $data[$this.attr('name')]['invoice_padding'] = $this.attr('data-invoice-padding');
                            }
                        }
                    }
                    if( $super_field.hasClass('super-auto-suggest') ) {
                        var $value = $super_field.find('.super-field-wrapper .super-dropdown-ui > .super-active').attr('data-value');
                        if( typeof $value !== 'undefined' ) {
                            $data[$this.attr('name')]['value'] = $value;
                        }
                    }
                    if( $super_field.hasClass('super-dropdown') ) {
                        var $i = 0;
                        var $new_value = '';
                        var $selected_items = $super_field.find('.super-field-wrapper .super-dropdown-ui > .selected');
                        $selected_items.each(function(){
                            if($i==0){
                                $new_value += $(this).text();
                                if($this.data('admin-email-value')=='both') {
                                    $new_value += ' ('+$(this).data('value')+')';
                                }
                            }else{
                                $new_value += ', '+$(this).text();
                                if($this.data('admin-email-value')=='both') {
                                    $new_value += ' ('+$(this).data('value')+')';
                                }
                            }
                            $i++;
                        });
                        $data[$this.attr('name')]['option_label'] = $new_value;
                        if( ($this.data('admin-email-value')=='label') || ($this.data('admin-email-value')=='both') ) {
                            $data[$this.attr('name')]['admin_value'] = $new_value; 
                        }else{
                            var $i = 0;
                            var $new_value = '';
                            $selected_items.each(function(){
                                var $item_value = $(this).data('value').toString().split(';');
                                if($i==0){
                                    $new_value += $item_value[0];
                                }else{
                                    $new_value += ', '+$item_value[0];
                                }
                                $i++;
                            });
                            $data[$this.attr('name')]['value'] = $new_value; 
                        }
                        var $email_value = $this.data('confirm-email-value');
                        if( ($email_value=='label') || ($email_value=='both') ) {
                            var $i = 0;
                            var $new_value = '';
                            $selected_items.each(function(){
                                var $item_value = $(this).data('value').toString().split(';');
                                if($i==0){
                                    $new_value += $(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }else{
                                    $new_value += ', '+$(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }
                                $i++;
                            });
                            $data[$this.attr('name')]['confirm_value'] = $new_value; 
                        }
                        var $email_value = $this.data('contact-entry-value');
                        if( ($email_value=='label') || ($email_value=='both') ) {
                            var $i = 0;
                            var $new_value = '';
                            $selected_items.each(function(){
                                var $item_value = $(this).data('value').toString().split(';');
                                if($i==0){
                                    $new_value += $(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }else{
                                    $new_value += ', '+$(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }
                                $i++;
                            });
                            $data[$this.attr('name')]['entry_value'] = $new_value; 
                        }
                    }
                    if( $super_field.hasClass('super-checkbox') || $super_field.hasClass('super-radio') ) {
                        var $i = 0;
                        var $new_value = '';
                        var $selected_items = $super_field.find('.super-field-wrapper .super-selected');
                        $selected_items.each(function(){
                            var $item_value = $(this).find('input').val().toString().split(';');
                            if($i==0){
                                $new_value += $(this).text();
                                if($this.data('admin-email-value')=='both') {
                                    $new_value += ' ('+$item_value[0]+')';
                                }
                            }else{
                                $new_value += ', '+$(this).text();
                                if($this.data('admin-email-value')=='both') {
                                    $new_value += ' ('+$item_value[0]+')';
                                }
                            }
                            $i++;
                        });
                        $data[$this.attr('name')]['option_label'] = $new_value;
                        if( ($this.data('admin-email-value')=='label') || ($this.data('admin-email-value')=='both') ) {
                            $data[$this.attr('name')]['admin_value'] = $new_value; 
                        }else{
                            var $i = 0;
                            var $new_value = '';
                            $selected_items.each(function(){
                                var $item_value = $(this).find('input').val().toString().split(';');
                                if($i==0){
                                    $new_value += $item_value[0];
                                }else{
                                    $new_value += ','+$item_value[0];
                                }
                                $i++;
                            });
                            $data[$this.attr('name')]['value'] = $new_value; 
                        }
                        var $email_value = $this.data('confirm-email-value');
                        if( ($email_value=='label') || ($email_value=='both') ) {
                            var $i = 0;
                            var $new_value = '';
                            $selected_items.each(function(){
                                var $item_value = $(this).find('input').val().toString().split(';');
                                if($i==0){
                                    $new_value += $(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }else{
                                    $new_value += ', '+$(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }
                                $i++;
                            });
                            $data[$this.attr('name')]['confirm_value'] = $new_value; 
                        }
                        var $email_value = $this.data('contact-entry-value');
                        if( ($email_value=='label') || ($email_value=='both') ) {
                            var $i = 0;
                            var $new_value = '';
                            $selected_items.each(function(){
                                var $item_value = $(this).find('input').val().toString().split(';');
                                if($i==0){
                                    $new_value += $(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }else{
                                    $new_value += ', '+$(this).text();
                                    if($email_value=='both') {
                                        $new_value += ' ('+$item_value[0]+')';
                                    }
                                }
                                $i++;
                            });
                            $data[$this.attr('name')]['entry_value'] = $new_value; 
                        }
                    }
                    if( $this.hasClass('super-keyword') ) {
                        var $parent = $this.parent().find('.super-entered-keywords');
                        var $tags = '';
                        var $counter = 0;
                        $parent.children('span').each(function(){
                            if($counter==0){
                                $tags += $(this).text();
                            }else{
                                $tags += ', '+$(this).text();
                            }
                            $counter++;
                        });
                        $data[$this.attr('name')]['value'] = $tags; 
                    }
                    if( $super_field.hasClass('super-keyword-tags') ) {
                        var $i = 0;
                        var $new_value = '';
                        $super_field.find('.super-autosuggest-tags > div > span').each(function(){
                            if($i==0){
                                $new_value += $(this).data('value');
                            }else{
                                $new_value += ','+$(this).data('value');
                            }
                            $i++;
                        });
                        $data[$this.attr('name')]['value'] = $new_value; 
                    }
                }
            }
        });
        if($form.find('input[name="hidden_form_id"]').length != 0) {
            var $form_id = $form.find('input[name="hidden_form_id"]').val();
        }
        $data['hidden_form_id'] = { 
            'name':'hidden_form_id',
            'value':$form_id,
            'type':'form_id'
        };
        var $entry_id = '';
        if($form.find('input[name="hidden_contact_entry_id"]').length != 0) {
            var $entry_id = $form.find('input[name="hidden_contact_entry_id"]').val();
        }
        $data['hidden_contact_entry_id'] = { 
            'name':'hidden_contact_entry_id',
            'value':$entry_id,
            'type':'entry_id'
        };
        return {data:$data, form_id:$form_id, entry_id:$entry_id};
    }
    SUPER.after_form_data_collected_hook = function($data){
        var $functions = super_common_i18n.dynamic_functions.after_form_data_collected_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                $data = SUPER[value.name]($data);
            }
        });
        return $data;
    }
    SUPER.after_duplicate_column_fields_hook = function($this, $field, $counter, $column, $field_names, $field_labels){
        var $functions = super_common_i18n.dynamic_functions.after_duplicate_column_fields_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($this, $field, $counter, $column, $field_names, $field_labels);
            }
        });
    }
    SUPER.after_appending_duplicated_column_hook = function($form, $unique_field_names, $clone){
        var $functions = super_common_i18n.dynamic_functions.after_appending_duplicated_column_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($form, $unique_field_names, $clone);
            }
        });
    }
    SUPER.after_duplicating_column_hook = function($form, $unique_field_names, $clone){
        var $functions = super_common_i18n.dynamic_functions.after_duplicating_column_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($form, $unique_field_names, $clone);
            }
        });
    }
    SUPER.before_submit_button_click_hook = function(e, $this){
        var $proceed = true;
        var $functions = super_common_i18n.dynamic_functions.before_submit_button_click_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                $proceed = SUPER[value.name](e, $proceed, $this);
            }
        });
        return $proceed;
    }
    SUPER.after_preview_loaded_hook = function($form_id){
        var $functions = super_common_i18n.dynamic_functions.after_preview_loaded_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($form_id);
            }
        });
    }
    SUPER.after_form_cleared_hook = function($form){
        var $functions = super_common_i18n.dynamic_functions.after_form_cleared_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                SUPER[value.name]($form);
            }
        });
    }
    SUPER.before_scrolling_to_error_hook = function($form, $scroll){
        var $proceed = true;
        var $functions = super_common_i18n.dynamic_functions.before_scrolling_to_error_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                $proceed = SUPER[value.name]($proceed, $form, $scroll);
            }
        });
        return $proceed;
    }
    SUPER.before_scrolling_to_message_hook = function($form, $scroll){
        var $proceed = true;
        var $functions = super_common_i18n.dynamic_functions.before_scrolling_to_message_hook;
        jQuery.each($functions, function(key, value){
            if(typeof SUPER[value.name] !== 'undefined') {
                $proceed = SUPER[value.name]($proceed, $form, $scroll);
            }
        });
        return $proceed;
    }
    SUPER.google_maps_api = function(){};
    SUPER.google_maps_init = function($changed_field, $form){
        if(typeof $form === 'undefined'){
            var $form = SUPER.get_frontend_or_backend_form();
        }
        if($form.hasClass('super-multipart')){
            var $form = $form.parents('.super-form:eq(0)');
        }
        SUPER.google_maps_api.initAutocomplete($changed_field, $form);
        SUPER.google_maps_api.initMaps($changed_field, $form);
    }
    SUPER.google_maps_api.initMaps = function($changed_field, $form){
        if(typeof $changed_field === 'undefined') {
            $maps = $form.find('.super-google-map');
        }else{
            var $form = $changed_field.parents('.super-form:eq(0)');
            $maps = $form.find('.super-google-map[data-fields*="['+$changed_field.attr('name')+']"]');
        }
        $maps.each(function(){
            $data = $(this).children('textarea').val();
            var $data = jQuery.parseJSON($data);
            var $form_id = $form.find('input[name="hidden_form_id"]').val();
            var $zoom = parseFloat($data.zoom);
            var $address = $data.address;
            var $address_marker = $data.address_marker;
            var $polyline_stroke_weight = $data.polyline_stroke_weight;
            var $polyline_stroke_color = $data.polyline_stroke_color;
            var $polyline_stroke_opacity = $data.polyline_stroke_opacity;
            var $polyline_geodesic = $data.polyline_geodesic;
            var map = new google.maps.Map(document.getElementById('super-google-map-'+$form_id), {
              zoom: $zoom
            });
            var $center_based_on_address = true;
            if( $data.enable_polyline=='true' ) {
                var $polylines = $data.polylines.split('\n');
                var $Coordinates = [];
                var $lat_min = '',
                    $lat_max = '',
                    $lng_min = '',
                    $lng_max = '';
                $($polylines).each(function(index, value){
                    $coordinates = value.split("|");
                    var $lat = $coordinates[0];
                    var $lng = $coordinates[1];
                    var $regular_expression = /\{(.*?)\}/g;
                    if($regular_expression.exec($lat)!=null){
                        var $field_name = $lat.replace('{','').replace('}','');
                        var $field = $form.find('.super-shortcode-field[name="'+$field_name+'"]');
                        $lat = $form.find('.super-shortcode-field[name="'+$field_name+'"]').attr('data-lat');
                        if(typeof $lat === 'undefined'){
                            $lat = 0;
                        }
                    }
                    var $regular_expression = /\{(.*?)\}/g;
                    if($regular_expression.exec($lng)!=null){
                        var $field_name = $lng.replace('{','').replace('}','');
                        var $field = $form.find('.super-shortcode-field[name="'+$field_name+'"]');
                        $lng = $form.find('.super-shortcode-field[name="'+$field_name+'"]').attr('data-lng');
                        if(typeof $lng === 'undefined'){
                            $lng = 0;
                        }
                    }
                    var $lat = parseFloat($lat);
                    var $lng = parseFloat($lng);
                    if( $lat!=0 && $lng!=0 ) {
                        var marker = new google.maps.Marker({
                            position: {lat: $lat, lng: $lng},
                            map: map
                        });
                    }
                    $Coordinates.push({lat: $lat, lng: $lng});
                    if( $lat_min=='' ) {
                        $lat_min = $lat;
                        $lat_max = $lat;
                        $lng_min = $lng;
                        $lng_max = $lng;
                    } 
                    if($lat_min>$lat) $lat_min = $lat;
                    if($lat_max<$lat) $lat_max = $lat;
                    if($lng_min>$lng) $lng_min = $lng;
                    if($lng_max<$lng) $lng_max = $lng;
                });
                if( $lat_min==0 || $lat_max==0 || $lng_min==0 || $lng_max==0 ) {
                    map.setCenter(new google.maps.LatLng(
                        (($lat_max + $lat_min) / 2.0),
                        (($lng_max + $lng_min) / 2.0)
                    ));                    
                }else{
                    $center_based_on_address = false;
                    map.setCenter(new google.maps.LatLng(
                        (($lat_max + $lat_min) / 2.0),
                        (($lng_max + $lng_min) / 2.0)
                    ));
                    map.fitBounds(new google.maps.LatLngBounds(
                        new google.maps.LatLng($lat_min, $lng_min), // bottom left
                        new google.maps.LatLng($lat_max, $lng_max) //top right
                    ));
                    var Path = new google.maps.Polyline({
                        path: $Coordinates,
                        geodesic: $polyline_geodesic,
                        strokeColor: $polyline_stroke_color,
                        strokeOpacity: $polyline_stroke_opacity,
                        strokeWeight: $polyline_stroke_weight
                    });
                    Path.setMap(map);
                }
            }
            if( ($address!='') && ($center_based_on_address==true) ) {
                var geocoder = new google.maps.Geocoder();
                var $regular_expression = /\{(.*?)\}/g;
                $address = SUPER.update_variable_fields.replace_tags($form, $regular_expression, $address);
                if($address!=''){
                    geocoder.geocode( { 'address': $address}, function(results, status) {
                        if (status == 'OK') {
                            map.setCenter(results[0].geometry.location);
                            if( $address_marker=='true' ) {
                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location
                                });
                            }
                        } else {
                            alert('Geocode was not successful for the following reason: ' + status);
                        }
                    });
                }
            }
        });
    }
    SUPER.google_maps_api.initAutocomplete = function($changed_field, $form){
        $form.find('.super-address-autopopulate:not(.super-autopopulate-init)').each(function(){
            var $element = $(this);
            var $field = $element.find('.super-shortcode-field');
            $element.addClass('super-autopopulate-init');
            var $form = $element.parents('.super-form:eq(0)');
            var autocomplete = new google.maps.places.Autocomplete( $element[0], {types: ['geocode']} );
            autocomplete.addListener( 'place_changed', function () {
                var mapping = {
                    street_number: 'street_number',
                    route: 'street_name',
                    locality: 'city',
                    administrative_area_level_2: 'municipality',
                    administrative_area_level_1: 'state',
                    country: 'country',
                    postal_code: 'postal_code'
                };
                var place = autocomplete.getPlace();
                $field.val(place.formatted_address);
                var lat = autocomplete.getPlace().geometry.location.lat();
                var lng = autocomplete.getPlace().geometry.location.lng();
                $element.attr('data-lat', lat).attr('data-lng', lng);
                SUPER.google_maps_init($element, $form);
                $element.trigger('keyup');
                var $street_name = '';
                var $street_number = '';
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    var attribute = $element.data('map-'+mapping[addressType]);
                    if(typeof attribute !=='undefined'){
                        var attribute = attribute.split('|');
                        if(attribute[1]=='') attribute[1] = 'long';
                        var val = place.address_components[i][attribute[1]+'_name'];
                        if(attribute[0]=='street_name') $street_name = val;
                        if(attribute[0]=='street_number') $street_number = val;
                        var $input = $form.find('.super-shortcode-field[name="'+attribute[0]+'"]');
                        $input.val(val);
                        SUPER.after_dropdown_change_hook($input); // @since 3.1.0 - trigger hooks after changing the value
                    }
                }
                var attribute = $element.data('map-street_name_number');
                if( typeof attribute !=='undefined' ) {
                    var $address = '';
                    if( $street_name!='' ) $address += $street_name;
                    if( $address!='' ) {
                        $address += ' '+$street_number;
                    }else{
                        $address += $street_number;
                    } 
                    var attribute = attribute.split('|');
                    var $input = $form.find('.super-shortcode-field[name="'+attribute[0]+'"]');
                    $input.val($address);
                    SUPER.after_dropdown_change_hook($input); // @since 3.1.0 - trigger hooks after changing the value
                }
                var attribute = $element.data('map-street_number_name');
                if( typeof attribute !=='undefined' ) {
                    var $address = '';
                    if( $street_number!='' ) $address += $street_number;
                    if( $address!='' ) {
                        $address += ' '+$street_name;
                    }else{
                        $address += $street_name;
                    } 
                    var attribute = attribute.split('|');
                    var $input = $form.find('.super-shortcode-field[name="'+attribute[0]+'"]');
                    $input.val($address);
                    SUPER.after_dropdown_change_hook($input); // @since 3.1.0 - trigger hooks after changing the value
                }
            });
        });
    }
    SUPER.checkboxes = function(){
        $('.super-checkbox').each(function(){
            var $value = '';
            var $counter = 0;
            var $checked = $(this).find('input[type="checkbox"]:checked');
            $checked.each(function () {
                if ($counter == 0) $value = $(this).val();
                if ($counter != 0) $value = $value + ',' + $(this).val();
                $counter++;
            });
            $(this).find('input[type="hidden"]').val($value);
        });
        $('.super-radio, .super-shipping').each(function(){
            var $name = $(this).find('.super-shortcode-field').attr('name');
            $(this).find('input[type="radio"]').attr('name','group_'+$name);
        });
        $('.super-shipping').each(function(){
            if(!$(this).hasClass('html-finished')){
                var $currency = $(this).find('.super-shortcode-field').attr('data-currency');
                $(this).find('input[type="radio"]').each(function(){
                    var $html = $(this).parent().html();
                    var $value = $(this).val();
                    $(this).parent().html($html+'<span class="super-shipping-price"> &#8212; '+$currency+''+parseFloat($value).toFixed(2)+'</span>');
                });
                $(this).addClass('html-finished');
            }        
        });
    }
    SUPER.reverse_columns = function($form){
        $form.find('.super-grid').each(function(){
            var $grid = $(this);
            var $columns = $grid.children('div.super-column:not(.super-not-responsive)');
            $grid.append($columns.get().reverse());
            $grid.children('div.super-column:last-child').removeClass('first-column');
            $grid.children('div.super-column:eq(0)').addClass('first-column');
        });
    }
    SUPER.handle_columns = function(){
        var $preload = super_common_i18n.preload;
        $('div.super-field').each(function(){
            if($(this).hasClass('grouped')){
                if((!$(this).prev().hasClass('grouped')) || ($(this).prev().hasClass('grouped-end'))){
                    $(this).addClass('grouped-start'); 
                }
            }
        });
        var $width = 0;
        $('.super-field > .super-label').each(function () {
            if($(this).parent().index()); 
            if (!$(this).parent().hasClass('grouped')) {
                if ($(this).outerWidth(true) > $width) $width = $(this).outerWidth(true);
            }
        });
        SUPER.checkboxes();
        SUPER.generateBarcode();
        SUPER.rating();
        $('.super-form').each(function () {
            var $this = $(this);
            if( $this.hasClass('super-rtl') ) {
                SUPER.reverse_columns($this);
            }
            var $exclusion = super_common_i18n.tab_index_exclusion;
            $fields = $($this.find('.super-field:not('+$exclusion+')').get());
            $fields.each(function(key, value){
                $(value).attr('data-super-tab-index', key);
            });
            if( $this.hasClass('super-rtl') ) {
                SUPER.reverse_columns($this);
            }
            $this.addClass('rendered');
            if (!$this.hasClass('preload-disabled')) {
                if (!$this.hasClass('initialized')) {
                    setTimeout(function (){
                        $this.fadeOut(100, function () {
                            $this.addClass('initialized').fadeIn(500);
                        });
                    }, 500);
                }
            } else {
                $this.addClass('initialized');
            }
            SUPER.after_initializing_forms_hook(undefined, $this);
        });
    }
    SUPER.remove_super_form_classes = function($this, $classes){
        $.each($classes, function( k, v ) {
            $this.removeClass(v);
        });
    }
    SUPER.init_replace_html_tags = function($changed_field, $form){
        if(typeof $form === 'undefined'){
            var $form = SUPER.get_frontend_or_backend_form();           
        }
        if(typeof $changed_field === 'undefined') {
            $html_fields = $form.find('.super-html-content');
        }else{
            var $form = $changed_field.parents('.super-form:eq(0)');
            $html_fields = $form.find('.super-html-content[data-fields*="['+$changed_field.attr('name')+']"]');
        }
        $html_fields.each(function(){
            var $target = $(this);
            var $html = $target.parent().children('textarea').val();
            if( $html!='' ) {
                var $regular_expression = /\{(.*?)\}/g;
                var $array = [];
                var $value = '';
                var $counter = 0;
                while (($match = $regular_expression.exec($html)) != null) {
                    $array[$counter] = $match[1];
                    $counter++;
                }
                if( $array.length>0 ) {
                    for (var $counter = 0; $counter < $array.length; $counter++) {
                        var $values = $array[$counter];
                        var $new_value = SUPER.update_variable_fields.replace_tags($form, $regular_expression, '{'+$values+'}', $target);
                        $html = $html.replace('{'+$values+'}', $new_value);
                        $target.html($html);
                    }
                }
            }
        });
    }
    SUPER.init_text_editors = function(){
        if( typeof tinyMCE !== 'undefined' ) {
            $('.super-text-editor:not(.initialized)').each(function(){
                var $this = $(this);
                var $form = $this.parents('.super-form:eq(0)');
                var $name = $this.attr('id');
                var $incl_url = $this.data('incl-url');
                tinyMCE.execCommand('mceRemoveEditor', true, $name);
                tinyMCEPreInit = {
                    baseURL: $this.data('baseurl'),
                    suffix: '.min',
                    mceInit: {},
                    qtInit: {},
                    ref: {},
                    load_ext: function(url,lang){
                        var sl=tinyMCE.ScriptLoader;
                        sl.markDone(url+'/langs/'+lang+'.js');
                        sl.markDone(url+'/langs/'+lang+'_dlg.js');
                    }
                };
                tinyMCEPreInit.mceInit[$name] = {
                    theme:"modern",
                    skin:"lightgray",
                    language:"en",
                    formats:{
                        alignleft: [{
                            selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", 
                            styles: {
                                textAlign:"left"
                            }
                        },{
                            selector: "img,table,dl.wp-caption", 
                            classes: "alignleft"
                        }],
                        aligncenter: [{
                            selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", 
                            styles: {
                                textAlign:"center"
                            }
                        },{
                            selector: "img,table,dl.wp-caption", 
                            classes: "aligncenter"
                        }],
                        alignright: [{
                            selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", 
                            styles: {
                                textAlign:"right"
                            }
                        },{
                            selector: "img,table,dl.wp-caption", 
                            classes: "alignright"
                        }],strikethrough: {
                            inline: "del"
                        }
                    },
                    relative_urls:false,
                    remove_script_host:false,
                    convert_urls:false,
                    browser_spellcheck:true,
                    fix_list_elements:true,
                    entities:"38,amp,60,lt,62,gt",
                    entity_encoding:"raw",
                    keep_styles:false,
                    cache_suffix:"wp-mce-4310-20160418",
                    preview_styles:"font-family font-size font-weight font-style text-decoration text-transform",
                    end_container_on_empty_block:true,
                    wpeditimage_disable_captions:false,
                    wpeditimage_html5_captions:true,
                    plugins:"charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
                    wp_lang_attr:"en-US",
                    content_css:$incl_url+"/css/dashicons.min.css,"+$incl_url+"/js/tinymce/skins/wordpress/wp-content.css",
                    selector:"#"+$name,
                    resize:"vertical",
                    menubar:false,
                    wpautop:false,
                    indent:false,
                    toolbar1:"bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv",
                    toolbar2:"formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
                    toolbar3:"",
                    toolbar4:"",
                    tabfocus_elements:":prev,:next",
                    body_class:$name+" post-type-page post-status-publish locale-en-us"
                };
                tinyMCEPreInit.qtInit[$name] = {
                    id:$name,
                    buttons:"strong,em,link,block,del,ins,img,ul,ol,li,code,more,close"
                };
                tinyMCEPreInit.ref = {
                    plugins:"charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
                    theme:"modern",
                    language:"en"
                };
                if( ($this.data('teeny')=='true') || ($this.data('teeny')==true) ){
                    tinyMCEPreInit.mceInit[$name].toolbar2 = false;
                }
                if( ($this.data('force-br')=='true') || ($this.data('force-br')==true) ){
                    tinyMCEPreInit.mceInit[$name].forced_root_block = false;
                    tinyMCEPreInit.mceInit[$name].force_br_newlines = true;
                    tinyMCEPreInit.mceInit[$name].force_p_newlines = false;
                    tinyMCEPreInit.mceInit[$name].convert_newlines_to_brs = true;
                }
                var init, id, $wrap;
                for ( id in tinyMCEPreInit.mceInit ) {
                    init = tinyMCEPreInit.mceInit[id];
                    $wrap = tinyMCE.$( '#wp-' + id + '-wrap' );
                    if ( ( $wrap.hasClass( 'tmce-active' ) || ! tinyMCEPreInit.qtInit.hasOwnProperty( id ) ) && ! init.wp_skip_init ) {
                        tinyMCE.init( init );
                        if ( ! window.wpActiveEditor ) {
                            window.wpActiveEditor = id;
                        }
                    }
                }
                for ( id in tinyMCEPreInit.qtInit ) {
                    quicktags( tinyMCEPreInit.qtInit[id] );
                    if ( ! window.wpActiveEditor ) {
                        window.wpActiveEditor = id;
                    }
                }
            });
        }
    }
    SUPER.init_set_dropdown_placeholder = function($form){
        if(typeof $form === 'undefined') var $form = $('.super-form');
        $form.find('.super-dropdown-ui').each(function(){
            var $this = $(this);
            var $field = $this.parent('.super-field-wrapper').find('.super-shortcode-field');
            var $first_item = $this.children('li:eq(1)');
            var $value = $field.val();
            if($value==''){
                var $value = $field.data('default-value');
            }
            if( (typeof $value !== 'undefined') &&  ($value!='') ) {
                $field.val($value);
                var $new_placeholder = '';
                var $value = $value.toString().split(',');
                var $i = 0;
                $.each($value, function( index, value ) {
                    value = $.trim(value);
                    var $item = $this.children('li[data-value="'+value+'"]:not(.super-placeholder)');
                    if($item.length){
                        var $name = $this.children('li[data-value="'+value+'"]').html();
                        if($i==0){
                            $new_placeholder += $name;
                        }else{
                            $new_placeholder += ', '+$name;
                        }
                        $item.addClass('selected');
                        $i++;
                    }
                });
                $this.children('.super-placeholder').html($new_placeholder);
            }else{
                $field.val('');
                var $placeholder = $field.attr('placeholder');
                if( (typeof $placeholder !== 'undefined') &&  ($placeholder!='') ) {
                    $this.children('.super-placeholder').attr('data-value', '').html($placeholder);
                }else{
                    if($this.children('.super-placeholder').html()==''){
                        $first_item.addClass('selected');
                        $this.children('.super-placeholder').attr('data-value', $first_item.attr('data-value')).html($first_item.html());
                    }
                }
            }
        });
    }
    SUPER.init_print_form = function($form, $submit_button){
        var win = window.open('','printwindow');
        var $html = '';
        var $print_file = $submit_button.find('input[name="print_file"]');
        if( (typeof $print_file.val() !== 'undefined') && ($print_file.val()!='') && ($print_file.val()!='0') ) {
            var $file_id = $print_file.val();
            $data = SUPER.prepare_form_data($form);
            var $form_id = $data.form_id;
            $data = SUPER.after_form_data_collected_hook($data.data);
            $.ajax({
                url: super_common_i18n.ajaxurl,
                type: 'post',
                data: {
                    action: 'super_print_custom_html',
                    data: $data,
                    file_id: $file_id
                },
                success: function (result) {
                    win.document.write(result);
                    win.print();
                    win.close();
                    return false;          
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('Failed to process data, please try again');
                    return false;
                }
            });
        }else{
            $css = "<style type=\"text/css\">";
            $css += "body {font-family:Arial,sans-serif;color:#444;-webkit-print-color-adjust:exact;}";
            $css += "table {font-size:12px;}";
            $css += "table th{text-align:right;font-weight:bold;font-size:12px;padding-right:5px;}";
            $css += "table td{font-size:12px;}";
            $css += "</style>";
            var $html = $css;
            $html += '<table>';
            $form.find('.super-shortcode-field').each(function(){           
                if( ($(this).attr('name')=='hidden_form_id') || ($(this).attr('name')=='id') ) return true;
                var $parent = $(this).parents('.super-shortcode:eq(0)');
                $html += '<tr>';
                $html += '<th>';
                $html += $(this).data('email');
                $html += '</th>';
                $html += '<td>';
                    if($parent.hasClass('super-radio')){
                        $html += $parent.find('.super-selected').text();
                    }else if($parent.hasClass('super-dropdown')){
                        var $items = '';
                        $parent.find('.super-dropdown-ui .selected').each(function(){
                            if($items==''){
                                $items += $(this).text();
                            }else{
                                $items += ', '+$(this).text();
                            }
                        });
                        $html += $items;
                    }else if($parent.hasClass('super-checkbox')){
                        var $items = '';
                        $parent.find('.super-selected').each(function(){
                            if($items==''){
                                $items += $(this).text();
                            }else{
                                $items += ', '+$(this).text();
                            }
                        });
                        $html += $items;
                    }else{
                        $html += $(this).val();
                    }
                $html += '</td>';
                $html += '</tr>';
            });
            $html += '</table>';
            win.document.write($html);
            win.print();
            win.close();
        }
    };
    SUPER.init_clear_form = function($form){
        $form.find('.super-shortcode-field').each(function(){
            if($(this).attr('name')=='hidden_form_id') return true;
            var $element = $(this);
            var $value = '';
            var $field = $element.parents('.super-field:eq(0)');
            var $default_value = $element.data('default-value');
            if( $field.hasClass('super-checkbox') || $field.hasClass('super-radio') ){
                $field.find('.super-field-wrapper > label').removeClass('super-selected');
                $field.find('.super-field-wrapper > label input').prop('checked', false);
                $field.find('.super-field-wrapper > label.super-default-selected').addClass('super-selected');  
                $field.find('.super-field-wrapper > label.super-default-selected input').prop('checked', true);
            }
            if($field.hasClass('super-toggle')){
                var $switch = $field.find('.super-toggle-switch');
                if($default_value==0){
                    $switch.removeClass('super-active');
                    var $toggle_value = $switch.find('.super-toggle-off').data('value');
                }else{
                    $switch.addClass('super-active');
                    var $toggle_value = $switch.find('.super-toggle-on').data('value');
                }
                $element.val($toggle_value);
                return true;
            }
            if($field.hasClass('super-dropdown')){
                $field.find('.super-dropdown-ui > li').removeClass('selected');
                $field.find('.super-dropdown-ui > li.super-default-selected').addClass('selected');
                if( (typeof $default_value !== 'undefined') && ($default_value!='') ) {
                    var $option = $field.find('.super-dropdown-ui > li[data-value="'+$default_value+'"]');
                    $field.find('.super-placeholder').html($option.text());
                    $option.addClass('selected');
                    $element.val($default_value);
                }else{
                    if($field.find('.super-dropdown-ui > li.selected').length==0){
                        if( (typeof $element.attr('placeholder') !== 'undefined') && ($element.attr('placeholder')!='') ) {
                            $field.find('.super-placeholder').html($element.attr('placeholder'));
                            $field.find('.super-dropdown-ui > li[data-value="'+$element.data('placeholder')+'"]').addClass('selected');
                        }else{
                            $field.find('.super-placeholder').html($field.find('.super-dropdown-ui > li:eq(0)').text());
                        }
                        $element.val('');
                    }else{
                        var $new_value = '';
                        var $new_placeholder = '';
                        $field.find('.super-dropdown-ui > li.selected').each(function(){
                            if($new_value==''){
                                $new_value += $(this).data('value');
                            }else{
                                $new_value += ','+$(this).data('value');
                            }
                            if($new_placeholder==''){
                                $new_placeholder += $(this).text();
                            }else{
                                $new_placeholder += ', '+$(this).text();
                            }
                        });
                        $field.find('.super-placeholder').html($new_placeholder);
                        $element.val($new_value);
                    }
                }
                return true;
            }
            if(typeof $default_value !== 'undefined'){
                $value = $default_value;
                $element.val($value);
                if($field.hasClass('super-slider')){
                    if($element.parent('.super-field-wrapper').children('.slider').length){
                        $element.simpleSlider("setValue", $value);
                    }
                    return true;
                }
                if($field.hasClass('super-rating')){
                    if($value==0){
                        $field.find('.super-rating-star').removeClass('selected');
                    }else{
                        var $rating = $field.find('.super-rating-star:eq('+($value-1)+')');
                        if($rating.length){
                            $field.find('.super-rating-star').removeClass('selected');
                            $rating.addClass('selected');
                            $rating.prevAll('.super-rating-star').addClass('selected');
                        }
                    }
                }
            }else{
                if($field.hasClass('super-countries')){
                    var $placeholder = $element.attr('placeholder');
                    if(typeof $placeholder === 'undefined' ) {
                        var $dropdown = $field.find('.super-dropdown-ui');
                        var $option = $field.find('.super-dropdown-ui > li:nth-child(2)');
                        $dropdown.children('li').removeClass('selected');
                        $dropdown.children('.super-default-selected').addClass('selected');
                        $dropdown.find('.super-placeholder').attr('data-value',$option.data('value')).html($option.html());
                        $element.val($option.data('value'));
                    }else{
                        var $dropdown = $field.find('.super-dropdown-ui');
                        $dropdown.children('li').removeClass('selected');
                        $dropdown.find('.super-placeholder').attr('data-value','').html($placeholder);
                        $element.val('');
                    }
                    return true;
                }
                if($field.hasClass('super-file')){
                    $field.find('.super-fileupload-files').html('');
                    $field.find('.super-progress-bar').attr('style','');
                    var $element = $field.find('.super-selected-files');
                    $element.val('');
                    return true;
                }
            }
            $element.val($value);
        });
        SUPER.after_field_change_blur_hook();
        SUPER.after_form_cleared_hook($form);
    }
    SUPER.populate_form_data = function($this, timeout){
        $this.attr('data-typing', 'true');
        if (timeout !== null) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(function () {
            $this.attr('data-typing', 'false');
            var $value = $this.val();
            var $method = $this.data('search-method');
            var $skip = $this.data('search-skip');
            if(typeof $skip === 'undefined' ) $skip = '';
            var $form = $this.parents('.super-form:eq(0)');
            if( $value.length>2 ) {
                $this.parents('.super-field-wrapper:eq(0)').addClass('super-populating');
                $form.addClass('super-populating');
                $.ajax({
                    url: super_common_i18n.ajaxurl,
                    type: 'post',
                    data: {
                        action: 'super_populate_form_data',
                        value: $value,
                        method: $method,
                        skip: $skip
                    },
                    success: function (result) {
                        var $data = jQuery.parseJSON(result);
                        if($data!=false){
                            var $dynamic_fields = {};
                            $form.find('.super-duplicate-column-fields').each(function(){
                                var $first_field = $(this).find('.super-shortcode-field:eq(0)');
                                var $first_field_name = $first_field.attr('name');
                                $dynamic_fields[$first_field_name] = $first_field;
                            });
                            $.each($dynamic_fields, function(index, field){
                                var $i = 2;
                                while(typeof $data[index+'_'+$i] !== 'undefined'){
                                    if($form.find('.super-shortcode-field[name="'+index+'_'+$i+'"]').length==0) {
                                        field.parents('.super-duplicate-column-fields:eq(0)').find('.super-add-duplicate').click();
                                    }
                                    $i++;
                                }
                            });
                            $.each($data, function(index, v){
                                if(v.name==$this.attr('name')){
                                    return true;
                                }
                                var $element = $form.find('.super-shortcode-field[name="'+v.name+'"]');
                                var $field = $element.parents('.super-field:eq(0)');
                                if(v.type=='files'){
                                    if((typeof v.files !== 'undefined') && (v.files.length!=0)){
                                        var $html = '';
                                        var $files = '';
                                        $.each(v.files, function( fi, fv ) {
                                            if(fi==0) {
                                                $files += fv.value;
                                            }else{
                                                $files += ','+fv.value;
                                            }
                                            $element = $form.find('.super-selected-files[name="'+fv.name+'"]');
                                            $field = $element.parents('.super-field:eq(0)');     
                                            $html += '<div data-name="'+fv.value+'" class="super-uploaded"';
                                            $html += ' data-url="'+fv.url+'"';
                                            $html += ' data-thumburl="'+fv.thumburl+'">';
                                            $html += '<span class="super-fileupload-name"><a href="'+fv.url+'" target="_blank">'+fv.value+'</a></span>';
                                            $html += '<span class="super-fileupload-delete">[x]</span>';
                                            $html += '</div>';
                                        });
                                        $element.val($files);
                                        $field.find('.super-fileupload-files').html($html);
                                        $field.find('.super-fileupload').addClass('finished');
                                    }else{
                                        $field.find('.super-fileupload-files').html('');
                                        $field.find('.super-progress-bar').attr('style','');
                                        var $element = $field.find('.super-selected-files');
                                        $element.val('');
                                    }
                                    return true;
                                }
                                var $value = v.value;
                                if($element.val()!=$value) $element.val($value);
                                if($field.hasClass('super-slider')){
                                    $element.simpleSlider("setValue", $value);
                                    return true;
                                }
                                if($field.hasClass('super-dropdown')){
                                    if($value!=''){
                                        var $options = $value.split(',');
                                        var $dropdown = $field.find('.super-dropdown-ui');
                                        $dropdown.children('li').removeClass('selected');
                                        $.each($options, function( index, v ) {
                                            $dropdown.children('li[data-value="'+v+'"]').addClass('selected');
                                        });
                                    }else{
                                        $field.find('.super-dropdown-ui > li').removeClass('selected');
                                        $field.find('.super-dropdown-ui > li.super-default-selected').addClass('selected');
                                    }
                                    SUPER.init_set_dropdown_placeholder();
                                    return true;
                                }
                                if($field.hasClass('super-radio')){
                                    var $wrapper = $field.find('.super-field-wrapper');
                                    var $labels = $wrapper.children('label');
                                    var $input = $labels.children('input');
                                    $labels.removeClass('super-selected');
                                    $input.prop('checked', false);
                                    if($value!=''){
                                        $labels.children('input[value="'+$value+'"]').prop('checked', false);
                                        $labels.children('input[value="'+$value+'"]').parents('label:eq(0)').addClass('super-selected');
                                    }else{
                                        $wrapper.find('label.super-default-selected').addClass('super-selected');  
                                        $wrapper.find('label.super-default-selected input').prop('checked', true);
                                    }
                                    return true;
                                }
                                if($field.hasClass('super-checkbox')){
                                    var $wrapper = $field.find('.super-field-wrapper');
                                    var $labels = $wrapper.children('label');
                                    var $input = $labels.children('input');
                                    $labels.removeClass('super-selected');
                                    $input.prop('checked', false);
                                    if($value!=''){
                                        var $options = $value.split(',');
                                        $.each($options, function( index, v ) {
                                            $labels.children('input[value="'+v+'"]').prop('checked', false);
                                            $labels.children('input[value="'+v+'"]').parents('label:eq(0)').addClass('super-selected');
                                        });
                                    }else{
                                        $wrapper.children('label.super-default-selected').addClass('super-selected');  
                                        $wrapper.children('label.super-default-selected input').prop('checked', true);
                                    }
                                    return true;
                                }
                                if($field.hasClass('super-rating')){
                                    var $rating = $field.find('.super-rating-star:eq('+($value-1)+')');
                                    if($rating.length){
                                        $field.find('.super-rating-star').removeClass('selected');
                                        $rating.addClass('selected');
                                        $rating.prevAll('.super-rating-star').addClass('selected');
                                    }
                                    return true;
                                }
                                if($field.hasClass('super-countries')){
                                    if($value!=''){
                                        var $options = $value.split(',');
                                        var $dropdown = $field.find('.super-dropdown-ui');
                                        $dropdown.children('li').removeClass('selected');
                                        $.each($options, function( index, v ) {
                                            $dropdown.children('li[data-value="'+v+'"]').addClass('selected');
                                        });
                                    }else{
                                        var $placeholder = $element.attr('placeholder');
                                        if(typeof $placeholder === 'undefined' ) {
                                            var $dropdown = $field.find('.super-dropdown-ui');
                                            var $option = $field.find('.super-dropdown-ui > li:nth-child(2)');
                                            $dropdown.children('li').removeClass('selected');
                                            $dropdown.children('.super-default-selected').addClass('selected');
                                            $dropdown.find('.super-placeholder').attr('data-value',$option.data('value')).html($option.html());
                                            $element.val($option.data('value'));
                                        }else{
                                            var $dropdown = $field.find('.super-dropdown-ui');
                                            $dropdown.children('li').removeClass('selected');
                                            $dropdown.find('.super-placeholder').attr('data-value','').html($placeholder);
                                            $element.val('');
                                        }
                                    }
                                    return true;
                                }
                            });
                            SUPER.after_field_change_blur_hook();
                        }
                    },
                    complete: function(){
                        $this.parents('.super-field-wrapper:eq(0)').removeClass('super-populating');
                        $form.removeClass('super-populating');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert('Failed to process data, please try again');
                    }
                });
            }
        }, 1000);
        return timeout;
    }
    SUPER.init_super_form_frontend = function(){
        $('.super-shortcode-field[data-search="true"]:not(.super-dom-populated)').each(function(){
            if($(this).val()!=''){
                $(this).addClass('super-dom-populated');
                SUPER.populate_form_data($(this), null);
            }
        });
        SUPER.init_text_editors();
        SUPER.init_fileupload_fields();
        SUPER.init_set_dropdown_placeholder($('.super-form:not(.rendered)'));
        $('.super-field.super-radio').each(function(){
            var $this = $(this);
            var $value = $this.find('.super-shortcode-field').val();
            if( typeof $value !== 'undefined' ) {
                var $value = $value.split(',');
                $this.find('input[type="radio"]').prop("checked", false);
                $.each($value, function( index, value ) {
                    value = $.trim(value);
                    $this.find('input[type="radio"][value="'+value+'"]').prop("checked", true);
                });
            }
        });
        $('.super-field.super-checkbox').each(function(){
            var $this = $(this);
            var $value = $this.find('.super-shortcode-field').val();
            if( typeof $value !== 'undefined' ) {
                var $value = $value.split(',');
                $this.find('input[type="checkbox"]').prop("checked", false);
                $.each($value, function( index, value ) {
                    value = $.trim(value);
                    $this.find('input[type="checkbox"][value="'+value+'"]').prop("checked", true);
                });
            }
        });
        $('.super-shortcode-field[data-mask]').each(function(){
            $(this).mask($(this).data('mask'));
        });
        $('.super-form').each(function(){
            var $form = $(this);
            var $total = $form.find('.super-multipart').length;
            if( $total!=0 ) {
                var $multipart = {};
                var $multiparts =  [];
                if( !$form.find('.super-multipart:eq(0)').hasClass('rendered') ) {
                    $form.find('.super-multipart:eq(0)').addClass('active').addClass('rendered');
                    var $submit_button = $form.find('.super-form-button:last');
                    $clone = $submit_button.clone();
                    $($clone).appendTo($form.find('.super-multipart:last'));
                    var $button_html = $submit_button.find('.super-button-name').html();
                    var $button_name = $submit_button.find('.super-button-name').text();
                    var $button_clone = $submit_button[0].outerHTML;
                    $submit_button.remove();
                    $($button_clone).appendTo($form.find('.super-multipart').not(':last')).removeClass('super-form-button').addClass('super-next-multipart').find('.super-button-name').html(super_common_i18n.directions.next);
                    $($button_clone).appendTo($form.find('.super-multipart').not(':first')).removeClass('super-form-button').addClass('super-prev-multipart').find('.super-button-name').html(super_common_i18n.directions.prev);
                    $form.find('.super-multipart').each(function(){
                        if( typeof $(this).data('prev-text') === 'undefined' ) {
                            var $prev = super_common_i18n.directions.prev;
                        }else{
                            var $prev = $(this).data('prev-text');
                        }
                        if( typeof $(this).data('next-text') === 'undefined' ) {
                            var $next = super_common_i18n.directions.next;
                        }else{
                            var $next = $(this).data('next-text');
                        }
                        $(this).find('.super-prev-multipart .super-button-name').html($prev);
                        $(this).find('.super-next-multipart .super-button-name').html($next);
                        $multipart = {
                            name: $(this).data('step-name'),
                            description: $(this).data('step-description'),
                            icon: $(this).data('icon'),
                        }
                        $multiparts.push($multipart);
                    });
                    var $progress_steps  = '<ul class="super-multipart-steps">';
                    $.each($multiparts, function( index, value ) {
                        if($total==1){
                            $progress_steps += '<li class="super-multipart-step active last-step">';
                        }else{
                            if((index==0) && ($total != (index+1))){
                                $progress_steps += '<li class="super-multipart-step active">';
                            }else{
                                if($total == (index+1)){
                                    $progress_steps += '<li class="super-multipart-step last-step">';
                                }else{
                                    $progress_steps += '<li class="super-multipart-step">';
                                }
                            }
                        }
                        $progress_steps += '<span class="super-multipart-step-wrapper">';
                        $progress_steps += '<span class="super-multipart-step-icon"><i class="fa fa-'+value.icon+'"></i></span>';
                        $progress_steps += '<span class="super-multipart-step-count">'+(index+1)+'</span>';
                        if( value.name!='' ) {
                            $progress_steps += '<span class="super-multipart-step-name">'+value.name+'</span>';
                        }
                        if( value.description!='' ) {
                            $progress_steps += '<span class="super-multipart-step-description">'+value.description+'</span>';
                        }
                        $progress_steps += '</span>';
                        $progress_steps += '</li>';
                    });
                    $progress_steps += '</ul>';
                    $form.prepend($progress_steps);
                    var $progress = 100 / $total;
                    var $progress_bar  = '<div class="super-multipart-progress">';
                        $progress_bar += '<div class="super-multipart-progress-inner">';
                        $progress_bar += '<div class="super-multipart-progress-bar" style="width:'+$progress+'%"></div>';
                        $progress_bar += '</div>';
                        $progress_bar += '</div>';
                    $form.prepend($progress_bar);
                }
            }
        });
        SUPER.init_super_responsive_form_fields();
        SUPER.init_tooltips();
        SUPER.init_distance_calculators();
        SUPERreCaptcha();
        SUPER.init_datepicker();
        SUPER.init_masked_input();
        SUPER.init_currency_input();
        SUPER.init_colorpicker();
        SUPER.init_button_colors();
        SUPER.init_skype();
        SUPER.init_dropdowns();
        SUPER.init_slider_field();
        SUPER.google_maps_init();
        SUPER.set_keyword_tags_width();
        $(window).resize(function() {
            SUPER.init_super_responsive_form_fields();
        });
        var $handle_columns_interval = setInterval(function(){
            if(($('.super-form').length != $('.super-form.rendered').length) || ($('.super-form').length==0)){
                SUPER.handle_columns();
            }else{
                clearInterval($handle_columns_interval);
            }
        }, 100);
    }
    SUPER.set_keyword_tags_width = function($field){
        if(typeof $field === 'undefined'){
            var $field = $('.super-form .super-keyword-tags');
        }
        $field.each(function(){
            var $this = $(this);
            var $width = $this.outerWidth(true);
            var $wrapper_width = $this.find('.super-field-wrapper').width();
            var $icon_width = 0;
            if($wrapper_width>=$width){
                var $icon_width = $this.find('.super-icon').outerWidth(true);
                var $width = $width-$icon_width;
            }else{
                $width = $wrapper_width;
            }
            var $autosuggest = $this.find('.super-autosuggest-tags.super-shortcode-field');
            $autosuggest.children('div').css('margin-left','');
            var $padding = $autosuggest.innerWidth() - $autosuggest.width();
            $width = $width - $padding + $icon_width;
            var $total_width = 0;
            $autosuggest.find('div > span').each(function(){
                $total_width = $total_width + $(this).outerWidth(true);
            });
            $autosuggest.children('input').css('width','0px');
            var $input_margins = $autosuggest.children('input').outerWidth(true);
            var $new_width = $width-$total_width-$input_margins-3;
            $autosuggest.children('input').css('width',$new_width+'px');
            var $min_input_width = parseFloat($autosuggest.width()/2).toFixed(0);
            var $min_input_width = parseFloat($min_input_width);
            if($total_width>$min_input_width){
                var $margin = $total_width - $min_input_width;
                $autosuggest.children('div').css('margin-left',-$margin+'px');
                $autosuggest.children('input').css('width',($min_input_width-$input_margins-3)+'px');
            }else{
                $autosuggest.children('div').css('margin-left','');
                $autosuggest.children('input').css('width',$new_width+'px');
            }
        });
    }
    SUPER.init_slider_field = function(){
        $('.super-slider').each(function () {
            var $this = $(this);
            if( $this.find('.slider').length==0 ) {
                var $field = $this.find('.super-shortcode-field');
                var $steps = $field.data('steps');
                var $min = $field.data('minnumber');
                var $max = $field.data('maxnumber');
                var $currency = $field.data('currency');
                var $format = $field.data('format');
                var $value = $field.val();
                var $decimals = $field.data('decimals');
                var $thousand_separator = $field.data('thousand-separator');
                var $decimal_separator = $field.data('decimal-separator');
                var $regular_expression = '\\d(?=(\\d{' + (3 || 3) + '})+' + ($decimals > 0 ? '\\D' : '$') + ')';
                var $number = parseFloat($value).toFixed(Math.max(0, ~~$decimals));
                var $number = ($decimal_separator ? $number.replace('.', $decimal_separator) : $number).replace(new RegExp($regular_expression, 'g'), '$&' + ($thousand_separator || ''));
                if( $value<$min ) {
                    $value = $min;
                }
                $field.simpleSlider({
                    snap: true,
                    step: $steps,
                    range: [$min, $max]
                });
                var $wrapper = $field.parents('.super-field-wrapper:eq(0)');
                var $slider = $wrapper.find('.slider');
                $wrapper.append('<span class="amount"><i>'+$currency+''+$value+''+$format+'</i></span>');
                $slider_width = $slider.outerWidth(true);
                $amount_width = $wrapper.children('.amount').outerWidth(true);
                $position = $slider.find('.dragger').position();
                if( (($position.left+$amount_width) + 5) < $slider_width ) {
                    $wrapper.children('.amount').css('left', $position.left+'px');
                }
                $field.bind("slider:changed", function (event, data) {
                    $slider_width = $slider.outerWidth(true);
                    $amount_width = $wrapper.children('.amount').outerWidth(true);
                    var $number = parseFloat(data.value).toFixed(Math.max(0, ~~$decimals));
                    var $number = ($decimal_separator ? $number.replace('.', $decimal_separator) : $number).replace(new RegExp($regular_expression, 'g'), '$&' + ($thousand_separator || ''));
                    $wrapper.children('.amount').children('i').html($currency+''+($number)+''+$format);
                    if( ((data.position+$amount_width) + 5) < $slider_width ) {
                        $wrapper.children('.amount').css('left', data.position+'px');
                    }
                });
            }
        });
        $('.slider-field').each(function () {
            var $this = $(this);
            if($this.children('.slider').length==0){
                var $field = $this.children('input');
                var $steps = $field.data('steps');
                var $min = $field.data('min');
                var $max = $field.data('max');
                $field.simpleSlider({
                    snap: true,
                    step: $steps,
                    range: [$min, $max]
                });
                $field.show();
            }
        });
    }
    SUPER.init_tooltips = function(){
        if ( $.isFunction($.fn.tooltipster) ) {
            $('.super-tooltip:not(.tooltipstered)').tooltipster({
                contentAsHTML: true,
            });
        }
    }
    SUPER.init_color_pickers = function(){
        if ( $.isFunction($.fn.wpColorPicker) ) {
            $('.super-color-picker').each(function(){
                if($(this).find('.wp-picker-container').length==0){
                    $(this).children('input').wpColorPicker({
                        palettes: ['#F26C68', '#444444', '#6E7177', '#FFFFFF', '#000000']
                    });
                }
            });
        }
    }
    SUPER.init_super_responsive_form_fields = function(){
        var $classes = [
            'super-first-responsiveness',
            'super-second-responsiveness',
            'super-third-responsiveness',
            'super-fourth-responsiveness',
            'super-last-responsiveness'
        ];
        var $window_classes = [
            'super-window-first-responsiveness',
            'super-window-second-responsiveness',
            'super-window-third-responsiveness',
            'super-window-fourth-responsiveness',
            'super-window-last-responsiveness'
        ];
        var $new_class = '';
        var $new_window_class = '';
        var $window_width = $(window).outerWidth(true);
        $('.super-form').each(function(){
            var $this = $(this);
            var $width = $(this).outerWidth(true);
            if($width > 0 && $width < 530){
                SUPER.remove_super_form_classes($this,$classes);
                $this.addClass($classes[0]);
                $new_class = $classes[0];
            }
            if($width >= 530 && $width < 760){
                SUPER.remove_super_form_classes($this,$classes);
                $this.addClass($classes[1]);
                $new_class = $classes[1];
            }
            if($width >= 760 && $width < 1200){
                SUPER.remove_super_form_classes($this,$classes);
                $this.addClass($classes[2]);
                $new_class = $classes[2];
            }
            if($width >= 1200 && $width < 1400){
                SUPER.remove_super_form_classes($this,$classes);
                $this.addClass($classes[3]);
                $new_class = $classes[3];
            }
            if($width >= 1400){
                SUPER.remove_super_form_classes($this,$classes);
                $this.addClass($classes[4]);
                $new_class = $classes[4];
            }
            if($window_width > 0 && $window_width < 530){
                SUPER.remove_super_form_classes($this,$window_classes);
                $this.addClass($window_classes[0]);
                $new_window_class = $window_classes[0];
            }
            if($window_width >= 530 && $window_width < 760){
                SUPER.remove_super_form_classes($this,$window_classes);
                $this.addClass($window_classes[1]);
                $new_window_class = $window_classes[1];
            }
            if($window_width >= 760 && $window_width < 1200){
                SUPER.remove_super_form_classes($this,$window_classes);
                $this.addClass($window_classes[2]);
                $new_window_class = $window_classes[2];
            }
            if($window_width >= 1200 && $window_width < 1400){
                SUPER.remove_super_form_classes($this,$window_classes);
                $this.addClass($window_classes[3]);
                $new_window_class = $window_classes[3];
            }
            if($window_width >= 1400){
                SUPER.remove_super_form_classes($this,$window_classes);
                $this.addClass($window_classes[4]);
                $new_window_class = $window_classes[4];
            }
            if( $this.hasClass('super-rtl') ) {
                if( (!$this.hasClass('super-rtl-reversed')) && ($new_class=='super-first-responsiveness') ) {
                    $this.find('.super-grid').each(function(){
                        var $grid = $(this);
                        var $columns = $grid.children('div.super-column:not(.super-not-responsive)');
                        $grid.append($columns.get().reverse());
                        $grid.children('div.super-column:last-child').removeClass('first-column');
                        $grid.children('div.super-column:eq(0)').addClass('first-column');
                    });
                    $this.addClass('super-rtl-reversed');
                }else{
                    if( ($this.hasClass('super-rtl-reversed')) && ($new_class!='super-first-responsiveness') ) {
                        $this.find('.super-grid').each(function(){
                            var $grid = $(this);
                            var $columns = $grid.children('div.super-column:not(.super-not-responsive)');
                            $grid.append($columns.get().reverse());
                            $grid.children('div.super-column:last-child').removeClass('first-column');
                            $grid.children('div.super-column:eq(0)').addClass('first-column');
                        });
                        $this.removeClass('super-rtl-reversed');
                    }
                }
            }
        });
        SUPER.set_keyword_tags_width();
        SUPER.after_responsive_form_hook($classes, $new_class, $window_classes, $new_window_class);
    }
    SUPER.init_field_filter_visibility = function($this) {
        if(typeof $this ==='undefined'){
            $('.super-elements-container .field.filter[data-filtervalue], .super-settings .super-field.filter[data-filtervalue]').addClass('hidden');
            $('.super-elements-container .field.filter[data-filtervalue], .super-settings .super-field.filter[data-filtervalue]').each(function(){
                var $this = $(this);
                var $container = $this.parents('.super-elements-container:eq(0)');
                if($container.length==0){
                    var $container = $this.parents('.super-settings:eq(0)');
                }
                var $parent = $this.data('parent');
                var $filtervalue = $this.data('filtervalue');
                var $parent = $container.find('.element-field[name="'+$parent+'"]');
                var $value = $parent.val();
                if(typeof $value==='undefined') var $value = '';
                var $parent = $parent.parents('.field.filter:eq(0)');
                var $visibility = $parent.hasClass('hidden');
                if($visibility==true){
                    $visibility = 'hidden';
                }else{
                    $visibility = 'visible';
                }
                var $filtervalues = $filtervalue.toString().split(',');
                var $string_value = $value.toString();
                var $match_found = false;
                $.each($filtervalues, function( index, value ) {
                    if( value==$string_value ) {
                        $match_found = true;
                    }
                });
                if( ($value!='') && ($match_found) && ($visibility!='hidden') ) {
                    $this.removeClass('hidden');
                }else{
                    $this.addClass('hidden');
                }
                SUPER.init_field_filter_visibility($this);
            });
        }else{
            var $name = $this.find('.element-field').attr('name');
            $('.super-elements-container .field[data-parent="'+$name+'"], .super-settings .super-field[data-parent="'+$name+'"]').each(function(){
                var $this = $(this);
                var $container = $this.parents('.super-elements-container:eq(0)');
                if($container.length==0){
                    var $container = $this.parents('.super-settings:eq(0)');
                }
                var $parent = $this.data('parent');
                var $filtervalue = $this.data('filtervalue');
                var $parent = $container.find('.element-field[name="'+$parent+'"]');
                var $value = $parent.val();
                if(typeof $value==='undefined') var $value = '';
                var $parent = $parent.parents('.field.filter:eq(0)');
                var $visibility = $parent.hasClass('hidden');
                if($visibility==true){
                    $visibility = 'hidden';
                }else{
                    $visibility = 'visible';
                }
                var $filtervalues = $filtervalue.toString().split(',');
                var $string_value = $value.toString();
                var $match_found = false;
                $.each($filtervalues, function( index, value ) {
                    if( value==$string_value ) {
                        $match_found = true;
                    }
                });
                if( ($value!='') && ($match_found) && ($visibility!='hidden') ) {
                    $this.removeClass('hidden');
                }else{
                    $this.addClass('hidden');
                }
                SUPER.init_field_filter_visibility($this);
            });
        }
    }
    SUPER.init_distance_calculators = function(){
        $('.super-form .super-text .super-distance-calculator').each(function() {
            var $this = $(this);
            var $form = $this.parents('.super-form:eq(0)');
            var $method = $this.data('distance-method');
            if($method=='start'){
                var $destination = $this.data('distance-destination');
                var $destination_field = $form.find('.super-shortcode-field[name="'+$destination+'"]');
                $destination_field.attr('data-distance-start',$this.attr('name'));
            }
        });
    }
    SUPER.super_find_next_tab_field = function($field, $form, $next_tab_index){
        if(typeof $next_tab_index === 'undefined'){
            var $next_tab_index_small_increment = parseFloat(parseFloat($field.attr('data-super-tab-index'))+0.001).toFixed(3);
            var $next_tab_index = parseFloat($field.attr('data-super-tab-index'))+1;
        }
        if(typeof $field.attr('data-super-custom-tab-index') !== 'undefined'){
            var $next_tab_index = parseFloat($field.attr('data-super-custom-tab-index'))+1;
        }
        var $next_tab_index_small_increment = parseFloat($next_tab_index_small_increment);
        var $next_tab_index = parseFloat(parseFloat($next_tab_index).toFixed(0));
        var $next_field_small_increment = $form.find('.super-field[data-super-tab-index="'+$next_tab_index_small_increment+'"]');
        if($next_field_small_increment.length){
            var $next_field = $next_field_small_increment;
        }else{
            var $next_field = $form.find('.super-field[data-super-tab-index="'+$next_tab_index+'"]');
        }
        var $next_custom_field = $form.find('.super-field[data-super-custom-tab-index="'+$next_tab_index+'"]');
        if( ($next_custom_field.length) && (!$next_custom_field.hasClass('super-focus')) ) {
            $next_field = $next_custom_field;
        }
        var $custom_tab_index = $next_field.attr('data-super-custom-tab-index');
        if(typeof $custom_tab_index !== 'undefined') {
            if($next_tab_index < parseFloat($custom_tab_index)){
                $next_field = SUPER.super_find_next_tab_field($field, $form, $next_tab_index+1);
            }
        }
        var $hidden = false;
        $next_field.parents('.super-shortcode.super-column').each(function(){
            if($(this).css('display')=='none'){
                $hidden = true;
            }
        });
        if( ( $next_field.css('display')=='none' ) || ( $next_field.hasClass('super-hidden') ) ) {
            $hidden = true;
        }
        var $parent = $next_field.parents('.super-shortcode:eq(0)');
        if( ( $hidden==true )  || ( ( $parent.css('display')=='none' ) && ( !$parent.hasClass('super-hidden') ) ) ) {
            $next_field = SUPER.super_find_next_tab_field($field, $form, $next_tab_index+1);
        }
        return $next_field;
    }
    SUPER.super_focus_next_tab_field = function(e, $next, $form, $skip_next){
        if(typeof $skip_next !== 'undefined'){
            var $next = $skip_next;
        }else{
            var $next = SUPER.super_find_next_tab_field($next, $form);
        }
        $form.find('.super-focus *').blur();
        $form.find('.super-focus').removeClass('super-focus');
        $form.find('.super-focus-dropdown').removeClass('super-focus-dropdown');
        $form.find('.super-color .super-shortcode-field').each(function(){
            $(this).spectrum("hide");
        });
        if( $next.hasClass('super-form-button') ) {
            $next.addClass('super-focus');
            SUPER.init_button_hover_colors( $next );
            $next.find('a').focus();
            e.preventDefault();
            return false;
        }
        if( $next.hasClass('super-next-multipart') ) {
            var keyCode = e.keyCode || e.which; 
            if (keyCode == 9) {
                $next.click().addClass('super-focus');
                SUPER.super_focus_next_tab_field(e, $next, $form);
            }
            e.preventDefault();
            return false;
        }
        if( $next.hasClass('super-color')) {
            $next.addClass('super-focus');
            $next.find('.super-shortcode-field').spectrum('show');
            e.preventDefault();
            return false;
        }
        if( ($next.hasClass('super-dropdown')) || ($next.hasClass('super-countries')) ) {
            $next.addClass('super-focus').addClass('super-focus-dropdown');
            if($next.find('input[name="super-dropdown-search"]').length){
                $next.find('input[name="super-dropdown-search"]').focus();
                e.preventDefault();
                return false;
            }
        }else{
            $next.addClass('super-focus');
        }
        $next.find('.super-shortcode-field').focus();
        e.preventDefault();
        return false;
    }
    jQuery(document).ready(function ($) {
        var $doc = $(document);
        var $super_hp = $doc.find('input[name="super_hp"]');
        window.setInterval(function() {
            $super_hp.each(function(){
                var hasValue = $(this).val().length > 0; //Normal
                if(!hasValue){
                    if($(this).is("\\:-webkit-autofill")) {
                        hasValue = true;
                    }
                }
                if (hasValue) {
                    $super_hp.val('');
                }
            });
        }, 1000);
        /*
        var $html = '';
        $('.super-calculator').each(function(){
            $html += '<strong>'+$(this).find('.super-shortcode-field').attr('name')+'</strong><br />';
            $html += $(this).find('.super-calculator-wrapper').attr('data-super-math')+'<br /><br />';
        });
        $('body').html($html);
        */
        /*
        $.getScript("https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js", function( data, textStatus, jqxhr ) {
            $.getScript("https://cdn.rawgit.com/MrRio/jsPDF/master/libs/html2pdf.js", function( data, textStatus, jqxhr ) {
                /*
                var pdf = new jsPDF('p', 'pt', 'letter');
                var options = {
                    background: '#fff' //background is transparent if you don't set it, which turns it black for some reason.
                };
                var items = document.querySelectorAll("super-multipart");
                var lastchild = items[items.length-1];
                pdf.addHTML(document.getElementsByClassName("super-multipart").lastChild[0], options, function () {
                    pdf.save('Test.pdf');
                });
                */
                /*
                var pdf = new jsPDF('p', 'pt', 'letter');
                pdf.canvas.height = 72 * 11;
                pdf.canvas.width = 72 * 8.5;
                html2pdf(document.body, pdf, function(pdf){
                    var iframe = document.createElement('iframe');
                    iframe.setAttribute('style','position:absolute;right:0; top:0; bottom:0; height:100%; width:500px');
                    document.body.appendChild(iframe);
                    iframe.src = pdf.output('datauristring');
                });
                */
                /*
                var doc = new jsPDF();
                doc.fromHTML('<style>.row {border:1px solid red;}</style>');
                doc.fromHTML('<styles>.row {border:1px solid red;}</styles>');
                doc.fromHTML('<div class="row">Test Row 1</div>');
                doc.fromHTML('<div class="row">Test Row 2</div>');
                doc.save('two-by-four.pdf');
                */
        /*
        setTimeout(function() {
            var myWindow=window.open();
            var $html = $('.super-multipart:last-child').html();
            myWindow.document.write($html);
            myWindow.document.close();
            myWindow.focus();
            setTimeout(function() {
                myWindow.print();
                myWindow.close();
            }, 250);
        }, 5000);
        */
        $doc.on('change', '.super-form .super-text .super-distance-calculator', function(){
            SUPER.calculate_distance($(this));
        });
        SUPER.init_field_filter_visibility();
        $doc.on('change keyup keydown blur','.field-container.filter, .field.filter, .super-field.filter',function(){
            SUPER.init_field_filter_visibility($(this));
        });  
        function super_update_dropdown_value(e, $dropdown, $key){
            var $input = $dropdown.find('.super-field-wrapper').children('input');
            var $parent = $dropdown.find('.super-dropdown-ui');
            var $placeholder = $parent.find('.super-placeholder');
            var $selected = $parent.find('.selected');
            var $multiple = false;
            if($parent.hasClass('multiple')) $multiple = true;
            if($multiple==false){
                var $value = $selected.attr('data-value');
                var $name = $selected.attr('data-search-value');
                $placeholder.html($name).attr('data-value',$value).addClass('selected');
                $parent.find('li').removeClass('selected');
                $selected.addClass('selected');
                $input.val($value);
            }else{
                var $max = $input.attr('data-maxlength');
                var $min = $input.attr('data-minlength');
                var $total = $parent.find('li.selected:not(.super-placeholder)').length;
                if($selected.hasClass('selected')){
                    if($total>1){
                        if($total <= $min) return false;
                        $selected.removeClass('selected');    
                    }
                }else{
                    if($total >= $max) return false;
                    $selected.addClass('selected');    
                }
                var $names = '';
                var $values = '';
                var $total = $parent.find('li.selected:not(.super-placeholder)').length;
                var $counter = 1;
                $parent.find('li.selected:not(.super-placeholder)').each(function(){
                    if(($total == $counter) || ($total==1)){
                        $names += $(this).attr('data-search-value');
                        $values += $(this).attr('data-value');
                    }else{
                        $names += $(this).attr('data-search-value')+', ';
                        $values += $(this).attr('data-value')+', ';
                    }
                    $counter++;
                });
                $placeholder.html($names);
                $input.val($values);
            }
            if($key=='enter') $dropdown.removeClass('super-focus-dropdown').removeClass('super-string-found');
            SUPER.after_dropdown_change_hook($input);
            e.preventDefault();
        }
        $doc.on('click', '.super-field.super-currency',function(){
            var $field = $(this);
            var $form = $field.parents('.super-form:eq(0)');
            $form.find('.super-focus').removeClass('super-focus');
            $form.find('.super-focus-dropdown').removeClass('super-focus-dropdown');
            $field.addClass('super-focus');
        });
        $doc.keydown(function(e){
            var keyCode = e.keyCode || e.which; 
            if (keyCode == 13) {
                var $dropdown = $('.super-focus-dropdown');
                if($dropdown.length){
                    super_update_dropdown_value(e, $dropdown, 'enter');
                }else{
                    var $element = $('.super-focus');
                    var $form = $element.parents('.super-form:eq(0)');
                    if($form.data('disable-enter')==true){
                        e.preventDefault();
                        return false;
                    }
                    var $element = $('.super-focus');
                    if( ($element.length) && (!$element.hasClass('super-textarea') ) ) {
                        if(!$form.find('.super-form-button.super-loading').length){
                            SUPER.before_validating_form_hook(undefined, $form);
                            $submit_button = $form.find('.super-form-button .super-button-wrap');
                            SUPER.validate_form( $form, $submit_button, undefined, e );
                            SUPER.after_validating_form_hook();
                        }
                        e.preventDefault();
                    }
                }
            }
            if ( (keyCode == 40) || (keyCode == 38) ) {
                var $dropdown = $('.super-focus-dropdown');
                if($dropdown.length){
                    var $placeholder = $dropdown.find('.super-dropdown-ui .super-placeholder');;
                    if(!$dropdown.find('.super-dropdown-ui .selected').length){
                        var $item = $dropdown.find('.super-dropdown-ui li:eq(1)');
                        if(keyCode == 38){
                            var $item = $dropdown.find('.super-dropdown-ui li:last-child');
                        }
                        $item.addClass('selected');
                        $placeholder.attr('data-value', $item.data('value')).html($item.html());
                    }else{
                        var $current = $dropdown.find('.super-dropdown-ui li.selected');
                        if(keyCode == 38){
                            var $next_index = $current.index() - 1;
                            if($next_index==0){
                                $next_index = $dropdown.find('.super-dropdown-ui li:last-child').index();
                            }
                        }else{
                            var $next_index = $current.index() + 1;
                        }
                        var $item = $dropdown.find('.super-dropdown-ui li:eq('+$next_index+')');
                        if($item.length==0){
                            var $item = $dropdown.find('.super-dropdown-ui li:eq(1)');
                        }
                        $dropdown.find('.super-dropdown-ui li.selected').removeClass('selected');
                        $placeholder.attr('data-value', $item.data('value')).html($item.html());
                        $item.addClass('selected');
                    }
                    var $dropdown_ui = $dropdown.find('.super-dropdown-ui');
                    $dropdown_ui.scrollTop($dropdown_ui.scrollTop() - $dropdown_ui.offset().top + $item.offset().top - 50); 
                    super_update_dropdown_value(e, $dropdown);
                }
            }
            if (keyCode == 9) {
                var $field = $('.super-field.super-focus');
                if( $field.length ) {
                    var $form = $field.parents('.super-form:eq(0)');
                    SUPER.super_focus_next_tab_field(e, $field, $form);
                }     
            }
        });
        $doc.on('keyup', '.super-icon-search input', function(){
            var $value = $(this).val();
            var $icons = $(this).parents('.super-icon-field').children('.super-icon-list').children('i');
            if($value==''){
                $icons.css('display','inline-block');   
            }else{
                $icons.each(function(){
                    if($(this).is('[class*="'+$value+'"]')) {
                        $(this).css('display','inline-block');
                    }else{
                        $(this).css('display','none');
                    }
                });
            }
        });
        $doc.on('click','.super-icon-list i',function(){
            if($(this).hasClass('active')){
                $(this).parent().find('i').removeClass('active');
                $(this).parents('.super-icon-field').find('input').val('');
            }else{
                $(this).parent().find('i').removeClass('active');
                $(this).parents('.super-icon-field').find('input').val($(this).attr('class').replace('fa fa-',''));
                $(this).addClass('active');
            }
        });
        var timeout = null;
        $doc.on('keyup', '.super-text .super-shortcode-field[data-search="true"]', function(){ 
            timeout = SUPER.populate_form_data($(this), timeout);
        });
        SUPER.init_slider_field();
        SUPER.init_tooltips();
        SUPER.init_distance_calculators();
        SUPER.init_color_pickers();
        SUPER.init_text_editors();
    });
})(jQuery);