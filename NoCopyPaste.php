<?php namespace INTERSECT\NoCopyPaste;

use \REDCap as REDCap;

class NoCopyPaste extends \ExternalModules\AbstractExternalModule {

protected static $Tags = array(
    '@NOPASTE' => array('description'=>'No Copy/Paste<br/>Prevents the user from pasting into a text field.'),
    '@NOPASTE-FORM' => array('description'=>'No Copy/Paste<br/>Prevents the user from pasting into a text field on a data entry form.'), 
    '@NOPASTE-SURVEY' => array('description'=>'No Copy/Paste<br/>Prevents the user from pasting into a text field on a survey.'),
    '@NOCOPY' => array('description'=>'No Copy/Paste<br/>Prevents the user from copying from a text field.'),
    '@NOCOPY-FORM' => array('description'=>'No Copy/Paste<br/>Prevents the user from copying from a text field on a data entry form.'), 
    '@NOCOPY-SURVEY' => array('description'=>'No Copy/Paste<br/>Prevents the user from copying from a text field on a survey.'),
);

protected function makeTagTR($tag, $description) {
                global $isAjax, $lang;
                return \RCView::tr(array(),
			\RCView::td(array('class'=>'nowrap', 'style'=>'text-align:center;background-color:#f5f5f5;color:#912B2B;padding:7px 15px 7px 12px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-right:0;'),
				((!$isAjax || (isset($_POST['hideBtns']) && $_POST['hideBtns'] == '1')) ? '' :
					\RCView::button(array('class'=>'btn btn-xs btn-rcred', 'style'=>'', 'onclick'=>"$('#field_annotation').val(trim('".js_escape($tag)." '+$('#field_annotation').val())); highlightTableRowOb($(this).parentsUntil('tr').parent(),2500);"), $lang['design_171'])
				)
			) .
			\RCView::td(array('class'=>'nowrap', 'style'=>'background-color:#f5f5f5;color:#912B2B;padding:7px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-left:0;border-right:0;'),
				$tag
			) .
			\RCView::td(array('style'=>'line-height:1.3;font-size:13px;background-color:#f5f5f5;padding:7px;border:1px solid #ccc;border-bottom:0;border-left:0;'),
				'<i class="fas fa-cube mr-1"></i>'.$description
			)
		);

}

public function redcap_every_page_before_render($project_id) {
    if (PAGE==='Design/action_tag_explain.php') {
        global $lang;
        $lastActionTagDesc = end(\Form::getActionTags());

        // which $lang element is this?
        $langElement = array_search($lastActionTagDesc, $lang);

        foreach (static::$Tags as $tag => $tagAttr) {
            $lastActionTagDesc .= "</td></tr>";
            $lastActionTagDesc .= $this->makeTagTR($tag, $tagAttr['description']);
        }
        $lang[$langElement] = rtrim(rtrim(rtrim(trim($lastActionTagDesc), '</tr>')),'</td>');
    }
}


    function getTags($tag) {
        // This is straight out of Andy Martin's example post on this:
        // https://community.projectredcap.org/questions/32001/custom-action-tags-or-module-parameters.html
        if (!class_exists('INTERSECT\NoCopyPaste\ActionTagHelper')) include_once('classes/ActionTagHelper.php');
        $action_tag_results = ActionTagHelper::getActionTags($tag);
        return $action_tag_results;
    }

    function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {

        // Get array of fields in current instruments
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Begin noPaste block
        $noPasteTags = array("@NOPASTE","@NOPASTE-SURVEY");
        $noPasteFields = array();

        foreach ($noPasteTags as $tag){
            $fields = $this->getTags($tag);
            if (empty($fields)) continue;
            $fields = array_keys($fields[$tag]);
            $noPasteFields = array_merge((array)$noPasteFields,(array)$fields); 
        };

        $noPasteFields = array_values(array_intersect((array)$noPasteFields, (array)$currInstrumentFields));
        // End noPaste block

        // Begin noCopy block
        $noCopyTags = array("@NOCOPY","@NOCOPY-SURVEY");
        $noCopyFields = array();

        foreach ($noCopyTags as $tag){
            $fields = $this->getTags($tag);
            if (empty($fields)) continue;
            $fields = array_keys($fields[$tag]);
            $noCopyFields = array_merge((array)$noCopyFields,(array)$fields); 
        };

        $noCopyFields = array_values(array_intersect((array)$noCopyFields, (array)$currInstrumentFields));
        // End noCopy block 

        // Create a JS array to feed into our JS script
        echo "<script type=\"text/javascript\">const noPasteFields = [];";
        for ($i = 0; $i < count($noPasteFields); $i++){
            // Push each field to the JS array
            echo "noPasteFields.push('". $noPasteFields[$i] ."');";
        }
        echo "const noCopyFields = [];";
        for ($i = 0; $i < count($noCopyFields); $i++){
            // Push each field to the JS array
            echo "noCopyFields.push('". $noCopyFields[$i] ."');";
        }
        // Inject function to add attributes to input fields
        echo "$(document).ready(function(){
            noPasteFields.forEach(function(field) {
                $('input[name=\"' + field + '\"]').attr('onpaste','return false').attr('ondrop','return false');
            });
            noCopyFields.forEach(function(field) {
                $('input[name=\"' + field + '\"]').attr('oncopy','return false').attr('ondragstart','return false');
            });
        });
        </script>";
    }

    function redcap_data_entry_form_top($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance) {
        // Get array of fields in current instruments
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Begin noPaste block
        $noPasteTags = array("@NOPASTE","@NOPASTE-FORM");
        $noPasteFields = array();

        foreach ($noPasteTags as $tag){
            $fields = $this->getTags($tag);
            if (empty($fields)) continue;
            $fields = array_keys($fields[$tag]);
            $noPasteFields = array_merge((array)$noPasteFields,(array)$fields); 
        };

        $noPasteFields = array_values(array_intersect((array)$noPasteFields, (array)$currInstrumentFields));
        // End noPaste block

        // Begin noCopy block
        $noCopyTags = array("@NOCOPY","@NOCOPY-FORM");
        $noCopyFields = array();

        foreach ($noCopyTags as $tag){
            $fields = $this->getTags($tag);
            if (empty($fields)) continue;
            $fields = array_keys($fields[$tag]);
            $noCopyFields = array_merge((array)$noCopyFields,(array)$fields); 
        };

        $noCopyFields = array_values(array_intersect((array)$noCopyFields, (array)$currInstrumentFields));
        // End noCopy block 

        // Create a JS array to feed into our JS script
        echo "<script type=\"text/javascript\">const noPasteFields = [];";
        for ($i = 0; $i < count($noPasteFields); $i++){
            // Push each field to the JS array
            echo "noPasteFields.push('". $noPasteFields[$i] ."');";
        }
        echo "const noCopyFields = [];";
        for ($i = 0; $i < count($noCopyFields); $i++){
            // Push each field to the JS array
            echo "noCopyFields.push('". $noCopyFields[$i] ."');";
        }
        // Inject function to add attributes to input fields
        echo "$(document).ready(function(){
            noPasteFields.forEach(function(field) {
                $('input[name=\"' + field + '\"]').attr('onpaste','return false').attr('ondrop','return false');
            });
            noCopyFields.forEach(function(field) {
                $('input[name=\"' + field + '\"]').attr('oncopy','return false').attr('ondragstart','return false');
            });
        });
        </script>";
    }
}
