/**
 * @author Kishor Mali
 */


jQuery(document).ready(function(){
	
	jQuery(document).on("click", ".deleteUser", function(){
		var userId = $(this).data("userid"),
			hitURL = baseURL + "deleteUser",
			currentRow = $(this);
		
		var confirmation = confirm("Are you sure to delete this user ?");
		
		if(confirmation)
		{
			jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { userId : userId } 
			}).done(function(data){
				console.log(data);
				currentRow.parents('tr').remove();
				if(data.status = true) { alert("User successfully deleted"); }
				else if(data.status = false) { alert("User deletion failed"); }
				else { alert("Access denied..!"); }
			});
		}
	});

	jQuery(document).on("click", ".deleteCategory", function(){
		var categoryId = $(this).data("categoryid"),
			hitURL = baseURL + "deleteCategory",
			currentRow = $(this);
		
		var confirmation = confirm("Are you sure to delete this Category ?");
		
		if(confirmation)
		{
			jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { categoryId : categoryId } 
			}).done(function(data){
				console.log(data);
				currentRow.parents('tr').remove();
				if(data.status = true) { alert("Category successfully deleted"); }
				else if(data.status = false) { alert("Category deletion failed"); }
				else { alert("Access denied..!"); }
			});
		}
	});

	jQuery(document).on("click", ".deleteImage", function(){
		var imageId = $(this).data("imageid"),
			hitURL = baseURL + "deleteImage",
			currentRow = $(this);
		
		var confirmation = confirm("Are you sure to delete this Image ?");
		
		if(confirmation)
		{
			jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { imageId : imageId } 
			}).done(function(data){
				console.log(data);
				currentRow.parents('tr').remove();
				if(data.status = true) { alert("Image successfully deleted"); }
				else if(data.status = false) { alert("Image deletion failed"); }
				else { alert("Access denied..!"); }
			});
		}
	});

	
	jQuery(document).on("click", ".deleteAds", function(){
		var adsId = $(this).data("adsid"),
			hitURL = baseURL + "deleteAds",
			currentRow = $(this);
		
		var confirmation = confirm("Are you sure to delete this Ad ?");
		
		if(confirmation)
		{
			jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : hitURL,
			data : { adsId : adsId } 
			}).done(function(data){
				console.log(data);
				currentRow.parents('tr').remove();
				if(data.status = true) { alert("Ad successfully deleted"); }
				else if(data.status = false) { alert("Ad deletion failed"); }
				else { alert("Access denied..!"); }
			});
		}
	});
	
	
	jQuery(document).on("click", ".searchList", function(){
		
	});
	
});
