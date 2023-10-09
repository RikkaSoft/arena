

$(".eventType").click(function(){
	$('#messageOutput');
	var eventType = $(this).attr('data-eventType');
	fetchMessages(eventType,0);
});

$("#loadMore").click(function(){
	var eventType = $(this).attr('data-eventType');
	var startId = $(this).attr('data-startId');
	fetchMessages(eventType,startId);
});

function fetchMessages(eventType,startId){
	$.get( 'index.php?mPage=loadMessages', { eventType: "type", start: startId } )
		.done(function( data ) {
			printRows(data);
		}
	, "json" );
}

function printRows(data){
	foreach(row in data){
		var messageRow = '<div class="messageRow" id='+row.id+'><div class="titleBox">'+row.title+'</div><div class="timestampBox">'+row.timestamp+'</div></div>';
		$('#messageOutput').append(messageRow);
	}
}