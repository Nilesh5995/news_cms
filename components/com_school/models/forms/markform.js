function save() {



      var id = jQuery("#jform_fname").val();
      var file = jQuery("#jform_imageinfo_image").prop('files')[0];
      var formdata = new FormData();
      formdata.append('id',id);
      formdata.append('file',file);  
      alert(formdata);  
    jQuery.ajax({
        type: "POST",
        url:"https://localhost/news_cms/index.php?option=com_school&task=markform.saves",
        data: {formdata:formdata},
        proccessData: false,
        contentType:false,
        success: function(data){
           alert(data);
          //jQuery("#searchresults").html(data);
         }
    });
}