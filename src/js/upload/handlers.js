function swfUploadLoaded(){
	var btnSubmit = document.getElementById("btnSubmit");
  btnSubmit.onclick  = doUpload;
}

function doUpload(){
  swfu.startUpload();
  return false;
}

function fileDialogStart(){

  var lv_file_name = document.getElementById("file_name");
	lv_file_name.value = "";

	this.cancelUpload();

}

function fileQueued(file){

	try {
		var lv_file_name = document.getElementById("file_name");
		lv_file_name.value = file.name;
	}catch(e){
	  alert("Error get file name");
	}

}

function fileQueueError(file, errorCode, message){
	try {
		// Handle this error separately because we don't want to create a FileProgress element for it.
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			alert("The file you selected is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			alert("The file you selected is empty.  Please select another file.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			alert("The file you choose is not an allowed file type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
		default:
			alert("An error occurred in the upload. Try again later.");
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
		}
	} catch (e) {
	}
}

function fileDialogComplete(numFilesSelected, numFilesQueued){

  var lv_file_name = document.getElementById("file_name");
  var lv_submit    = document.getElementById("btnSubmit");

  lv_active = true;
	if(lv_file_name.value === ""){
	  lv_active = false;
	}

  lv_submit.disabled = !lv_active;

}

function uploadStart(file){

  var lv_upload   = document.getElementById("upload");
  var lv_progress = document.getElementById("progress");

  //lv_upload.style.display   = "none";
  lv_progress.style.display = "block";

  var btnSubmit   = document.getElementById("btnSubmit");
  btnSubmit.disabled = true;

  this.setButtonDisabled(true);

}

function uploadProgress(file, bytesLoaded, bytesTotal) {

	try{

    document.getElementById("uploaded").innerHTML = SWFUpload.speed.formatBytes(file.sizeUploaded);
    document.getElementById("total").innerHTML    = SWFUpload.speed.formatBytes(file.size);
    document.getElementById("percent").innerHTML  = SWFUpload.speed.formatPercent(file.percentUploaded);
    document.getElementById("speed").innerHTML    = SWFUpload.speed.formatBPS(file.averageSpeed / 8);
    document.getElementById("time").innerHTML     = SWFUpload.speed.formatTime(file.timeRemaining);

  }catch(e){
  }

}

function uploadSuccess(file, serverData){

  try{

    this.customSettings.upload_successful = true;

    var lv_file_id = document.getElementById("file_id");
    lv_file_id.value = serverData;

	}catch(e){
	}

}

function uploadError(file, errorCode, message){

	try {

		if (errorCode === SWFUpload.UPLOAD_ERROR.FILE_CANCELLED) {
			// Don't show cancelled error boxes
			return;
		}
		
		// Handle this error separately because we don't want to create a FileProgress element for it.
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
			alert("There was a configuration error.  You will not be able to upload a resume at this time.");
			this.debug("Error Code: No backend file, File name: " + file.name + ", Message: " + message);
			return;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			alert("You may only upload 1 file.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			break;
		default:
			alert("An error occurred in the upload. Try again later.");
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			return;
		}

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error");
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			progress.setStatus("Upload Cancelled");
			this.debug("Error Code: Upload Cancelled, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Upload Stopped");
			this.debug("Error Code: Upload Stopped, File name: " + file.name + ", Message: " + message);
			break;
		}
	}catch(ex){
	}
}

function uploadComplete(file){

	try{

    var lv_upload    = document.getElementById("upload");
    var lv_progress  = document.getElementById("progress");
    var lv_file_name = document.getElementById("file_name");

    lv_upload.style.display   = "block";
    lv_progress.style.display = "none";

    this.setButtonDisabled(false);

		if(this.customSettings.upload_successful){

      this.customSettings.upload_successful = false;

      lv_file_name.value = "";

    	try{
    		document.forms["form_upload"].submit();
    	}catch(ex){
    		alert("Error submitting form");
    	}

		}else{
			alert("There was a problem with the upload.\nThe server did not accept it.");
		}

	}catch(e){
	}

}