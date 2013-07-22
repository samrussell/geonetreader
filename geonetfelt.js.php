<?
header("Content-Type: text/javascript");
header('Access-Control-Allow-Origin: *');
?>

// Written by Sam Russell in July, 2013
// Code based on "jQuery Twitter Feed Function" by Jay Blanchard, 2011

function getCoordsImage(coordinates){
    ew = coordinates[0].toFixed(0);
    ns = coordinates[1].toFixed(0);
    ewdir = (ew > 0)? 'E':'W';
    nsdir = (ns > 0)? 'N':'S';
    return 'http://static1.geonet.org.nz/maps/quake/hdpi/'+Math.abs(ew)+ewdir+Math.abs(ns)+nsdir+'.png';
}

function parseGNSFeature(feature){
    magnitude = feature.properties.magnitude.toFixed(1);
    coordinates = feature.geometry.coordinates[0] + ', ' + feature.geometry.coordinates[1];
    imgsrc = getCoordsImage(feature.geometry.coordinates);
    time = feature.properties.origintime;
    depth = feature.properties.depth.toFixed(0);
    publicid = feature.properties.publicid;
    
    toreturn = '';
    // image format: http://static1.geonet.org.nz/maps/quake/hdpi/175E40S.png
    toreturn += '<li>Magnitude: ' + magnitude + '</li>';
    toreturn += '<li>Time: ' + time + '</li>';
    toreturn = '<div style="float: left; width: 100px;"><a href="http://geonet.org.nz/quakes/region/newzealand/' + publicid + '"><img src="' + imgsrc + '"></a></div><div><ul>'+toreturn+'</ul></div>';
    return toreturn
}

$(document).ready(function() {
   
   var garys=0;
   var lastID = "";
   var recentautocall=0;
   
   function autoScroll(entry) {
   	var itemHeight = $('#eq li').outerHeight();
   		/* calculte how much to move the scroller */
       var moveFactor = parseInt($('#eq').css('top')) + itemHeight;
       /* animate the carousel */
       
       $('#eq li:first').html(entry);
       $('#eq').animate(
           {'top' : moveFactor}, 'slow', 'linear', function(){
               /* put the last item before the first item */
        	   //$("#eq li:first").before($("#eq li:last"));
               /* reset top position */       
               $('#eq').append('<li class="quake"></li>');       
               $('#eq').css({'top' : '-6em'});
               $("#eq li:first").before($("#eq li:last"));
       });
   };
   
   function checkUpdates(){
      //autoScroll('gary' + garys++);
      $.getJSON('jsonwrapperfelt.php' ,function(data){
        thisID = "";
        thisentry = "";
        for(var i=0;i<data.features.length && i<5;i++){
          feature = data.features[i];
          if(feature.properties.publicid == lastID){
            break;
          }
          thisID = feature.properties.publicid
          thisentry = parseGNSFeature(data.features[i]);
        }
        if(thisentry != ""){
          autoScroll(thisentry);
          lastID = thisID;
          recentautocall++;
          setTimeout(checkUpdates, 1000);
        }
      });
   }
   
   function handleCheckUpdates(){
     if(recentautocall==0){
       checkUpdates();
     }
     else{
       recentautocall=0;
     }
     setTimeout(handleCheckUpdates, 10000);
   }
   
   function preLoadCarousel(){
      $('#eq').append('<li class="quake"></li>');
   }
   
   /* make the carousel scroll automatically when the page loads */
   preLoadCarousel();
   handleCheckUpdates();
});