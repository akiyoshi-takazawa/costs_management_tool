$(function () {
    // yearのvalue値を取得
    var year = $('.year_month .year select').val();
        
       console.log(year);
       
    // monthのvalue値を取得  
     var month = $('.year_month .month select').val();
        
       console.log(month);
    
    // year or month で変更されたvalueを取得
    $('.year_month select').change(function() {
        
       var month = $('.year_month .month select').val();
       var year = $('.year_month .year select').val(); 
        var val = $(this).val();
       
       console.log(val);
       
          if (val > 1000){
              var year = val;
          }else{
              var month = val;
          }
       
       location.href = "/weeks/" + year + "/" + month;
       
       console.log(location.href)
        
    });
    
    
    $('.report-week select').change(function() {
        
       var inputweek = $('.report-week select').val();
       var val = $(this).val();
       
       console.log(inputweek);
       console.log(val);
       
      location.href = "/costs/" + inputweek;
       
    });
    
    
  
});

