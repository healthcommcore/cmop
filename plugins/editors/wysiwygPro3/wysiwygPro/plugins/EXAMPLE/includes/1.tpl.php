<?php if (!defined('IN_WPRO')) exit; ?>

<p><?php echo $message ?></p>
<textarea class="exampleTextarea" name="strToInsert"></textarea>

<script type="text/javascript">
/* When the dialog form is submitted a function called formAction is looked for and called if available.
It should return false if it does not want the form to actually be submitted. */
function formAction () {

	var html = document.dialogForm.strToInsert.value;
	
	dialog.editor.insertAtSelection(html);
	
	dialog.close();
	return false;
}

</script>
