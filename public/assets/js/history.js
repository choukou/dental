$(document).ready(function() {
   storage=$.localStorage;
   $('#clear-all-history').click(function(){
       
       // storage.removeAll();
       storage.remove(getApi());
       // location.reload();
   });
});

function saveHistory (key, value , comment) {
    var datetime = new Date().getTime();
    if(storage.isSet(key)){
        var NewValue = storage.get(key);
        // while (NewValue.length > 9){
            // NewValue.shift();
        // }
       NewValue.push([{"data" : value},{"dattime":datetime}, {"comment": comment}]);
       storage.set(key, NewValue);
    }else{
        storage.set(key,[[{"data" : value},{"dattime":datetime}, {"comment": comment}]]);
    }
}

function getListTimeForSave(api){
    $('#history-api').val(api);
    if(storage.isSet(api)){
         var Value = storage.get(api);
          for(key in Value){
              var date = new Date(Value[key][1].dattime);
              if(key == (Value.length - 1)){
                  $('#time-history').prepend("<option selected='selected' value ='"+ Value[key][1].dattime + "'>"+date+"</option>");
              }else{
                  $('#time-history').prepend("<option value ='"+ Value[key][1].dattime + "'>"+date+"</option>");
              }
          }
    }else{
    }

}

function getHistory () {
    var api = $('#history-api').val();
    var date = $('#time-history').val();
    if(storage.isSet(api)){
          var Value = storage.get(api);
          for(key in Value){
                 if(Value[key][1].dattime == date){
                      $('#comment-date').html(Value[key][2].comment);
                      $('#data-date').html(Value[key][0].data);
                 }
          }
          if(Value.length == 0){
              $('#comment-date').html('');
              $('#data-date').html('');
          }
    }else{
    }
}

function deleteHistoryByTime (time) {
  var api = $('#history-api').val();
  var Value = storage.get(api);
  var reValue = [];
  for(key in Value){
     if(Value[key][1].dattime != time){
          reValue.push([{"data" : Value[key][0].data},{"dattime":Value[key][1].dattime}, {"comment": Value[key][2].comment}]);
    }
  }
  storage.set(api, reValue);
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};