jQuery(window).bind("load", function() {
  if (click_video) {
      $( "a#play_video" ).trigger( "click" );
  } else {
    // do nothing
  }
});