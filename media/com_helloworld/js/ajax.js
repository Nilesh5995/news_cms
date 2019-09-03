
    // get the data passed from Joomla PHP
    // params is a Javascript object with properties for the map display: 
    // centre latitude, centre longitude and zoom, and the helloworld greeting
    //const params = Joomla.getOptions('params');
    
    // We'll use OpenLayers to draw the map (http://openlayers.org/)
    
    // Openlayers uses an x,y coordinate system for positions
    // We need to convert our lat/long into an x,y pair which is relative
    // to the map projection we're using, viz Spherical Mercator WGS 84
   function searchHere() {
      var id = jQuery("#jform_id").val();
      //alert(id);
    jQuery.ajax({
        type: "POST",
        url:"https://localhost/news_cms/index.php?option=com_helloworld&task=ajax.getData",
       //dataType: "json",
        //format: "json",
        data: { id:id},

        success: function(data){
           //alert(data);
          jQuery("#searchresults").html(data);
         }
    });
}
