var url = "http://en.wikipedia.org/wiki/";
var start;
var next;
var urlsVisited = new Array();
var result;
var i = 0;

function f(rand)
{
	i++;
	new Ajax.Request('ajax.php', {
								method:'get', 
								parameters:{
										data: next, 
										rand: rand
								}, 
								requestHeaders:{
											Accept: 'application/json'
								},
								onSuccess: function(transport){
													  var pika = transport.responseText.evalJSON(true);
													  if(pika == "error")
													  {
														  jQuery('span.result').html("Something went wrong."); 
														  jQuery('.loading').hide();
													  }															  
													  next = pika.next;
													  if (rand == true)
													  {
														start = pika.title;
														jQuery('span.start').html("<a href=" + url + pika.rand + " target=_blank>" + pika.title + "</a>");
													  }
													  else
													  {
														jQuery('span.title:last').html(pika.title);
													  }
													  if (urlsVisited.indexOf(pika.next) != -1)
													  { 
														jQuery('span.result').html("Loop encountered.");
														jQuery('.loading').hide();
														return;
													  }
													  
													  jQuery('#table').find('tbody').append("<tr><td>"+i+"</td><td><span class=title>"+next+"</span></td><td><a href="+url+next+" target=_blank>"+url+next+"</a></td></tr>");
													  jQuery('table').show();
													  
													  if (next == "philosophy" || next == "Philosophy")
													  {
														  jQuery('span.result').html(i+" click(s) from <a href="+url+start+" target=_blank>"+start+"</a>"+" to "+"<a href="+url+"Philosophy target=_blank>Philosophy</a>.");
														  jQuery('.loading').hide();
														  
														  new Ajax.Request('record.php', {
																					method:'post', 
																					parameters: {
																						pages: JSON.stringify(urlsVisited
																					},
																					onSuccess: function(transport){
																						console.log(transport.responseText.evalJSON(true));
																					}	
																			}
															);
														return;
														}
													  else
													  {
															  urlsVisited.push(pika.next);
															  f(false);
														  }
													 }
		}
	);
}

function validate()
{
	if (jQuery.trim(jQuery("#next").val()) != "") 
	{
		doStuff();
	}
}

function doStuff()
{
	var nameElement = document.getElementById("next");
	start = jQuery.trim(nameElement.value);	
	begin(false);
}

function begin(rand)
{
	next = start;
	document.getElementById("bn").disabled = true;
	document.getElementById("rand").disabled = true;
	jQuery('span.start').html("<a href=" + url + next + " target=_blank><span class=title>" + next + "</span></a>");				
	jQuery('div.start').show();
	jQuery('div.loading').show();
	urlsVisited.push(next);
	f(rand);
}

function rand()
{
	start = "Special:Random";
	begin(true);
}
