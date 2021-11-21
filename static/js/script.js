
function rateStars(id, i) {
   var starId = "star"+id;
   var ratingId = "rating"+id;
   var operationId = "operation"+id;
   var operation = "";
   const stars = [...document.getElementsByClassName(starId)];
   const activeStar = starId+" rating_star fas fa-star";
   const inactiveStar = starId+" rating__star far fa-star";
   const starsLength = stars.length;
   var rating = i;
   var oldRating = document.getElementById(ratingId).value;

    if(stars[i].className.includes(inactiveStar)){
        for (i; i >= 0; --i) stars[i].className = activeStar;
        document.getElementById(ratingId).value = ""+(rating+1);
    }else{
        for (i; i < starsLength; ++i) stars[i].className = inactiveStar;
        document.getElementById(ratingId).value = ""+(rating);
    }

    var newRating = document.getElementById(ratingId).value;
    if(newRating == "0"){//delete operation
        document.getElementById(operationId).value = "delete";
    }else if(oldRating == "0"){//add operation
        document.getElementById(operationId).value = "add";
    }else{//update operation
        document.getElementById(operationId).value = "update";
    }

}

function setRating(rating, id){

    var starId = "star"+id;
    var ratingId = "rating"+id;
    const stars = [...document.getElementsByClassName(starId)];
    const activeStar = starId+" rating_star fas fa-star";
    const starsLength = stars.length;
    var i = parseInt(rating) - 1;

    if(i != -1){
        for (i; i >= 0; --i) stars[i].className = activeStar;
        document.getElementById(ratingId).value = ""+(rating);
    }   
}


//Show status message to user for 5 seconds and then remove.
//A confirmation message is show when a comment is added, updated, or deleted.
function timedMsgById(msg, id) {
    document.getElementById(id).style.display = 'block';
    var div = document.getElementById(id);
    div.innerHTML = msg;
    setTimeout("document.getElementById('"+id+"').style.display = 'none';",5000);
    //After status message is shown, then set it to an empty string.
    setTimeout("document.getElementById('"+id+"').innerHTML = '';",5000);
}

function showMsgById(msg, id) {
    var div = document.getElementById(id);
    div.innerHTML = msg;
}

//Enables a disabled element such as a textarea.
function enableElementById(id){
    document.getElementById(id).disabled = false;
    document.getElementsByName
}

//Hides an element such as the edit or delete links for updating or deleting a comment.
function hideElementById(id){
    document.getElementById(id).style.display = 'none';
}

//Unhides an element
function showElementById(id){
    document.getElementById(id).style.display = 'inline-block';
}

function hideElementByClass(className){

    var elements = document.getElementsByClassName(className);
    for (var i = 0; i < elements.length; i ++) {
        elements[i].style.display = 'none';
    }

}

//Hides one or more elements of the specified class name
function hideElementsByName(name){
    var elems = document.getElementsByName(name);

    for(var i= 0; i <elems.length; i++){
        elems[i].style.display = 'none';
    }
}

function preview(id) {
   var img = document.getElementById(id);
   var fileName = event.target.files[0].name;
   var validext = "jpg,jpeg,gif,png,bmp";
   file = fileName;
   ext = file.split('.').pop().toLowerCase();
  
   
   if(parseInt(validext.indexOf(ext)) >= 0){
	   img.src= URL.createObjectURL(event.target.files[0]);
   }
}

function scrollToElemById(id){
   const elem = document.getElementById(id);
   elem.scrollIntoView(false); //false means scroll so that the element is at the bottom of the viewpoint
   
}
