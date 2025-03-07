var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
	acc[i].addEventListener("click", function(){
		this.classList.toggle("active");
		var faqext = this.nextElementSibling;
		if (faqext.style.maxHeight){
			faqext.style.maxHeight = null;
		}
		else{
		faqext.style.maxHeight = faqext.scrollHeight +"px";
	
		}
	});

}


