<!--
 DHCP Leases
 ARP List
 Static Addresses?
-->

<form id="fe">
<input type='hidden' id='dhcptable' name='dhcptable'>
<input type='hidden' id='act' name='act'>
	<div class='pageTitle'>Network: DHCP/Gateways</div>

<div class='controlBox'>
	<span class='controlBoxTitle'>Summary</span>
	<div class='controlBoxContent' id='devicelist'>
		<input type='button' id="savebutton" name="savebutton" value='Save' onclick='DHCPcall("save")'>
		<input type='button' id="cancelbutton" name="cancelbutton" value='Cancel' onclick='DHCPcall("get")'>
    <input type='button' id="refreshbutton" name="refreshbutton" value='Refresh' onclick='DHCPcall("get")'>
		<span id='messages'>&nbsp;</span>


		<div id='hideme'>
			<div class='centercolumncontainer'>
				<div class='middlecontainer'>
					<div id='hiddentext' value-'Please wait...' ></div>
					<br>
				</div>
			</div>
		</div>

		<div class="smallText">
			<br><b>Make Static</b>- Choose "on" to make lease permanent. </li>
      <br><b>Route</b>- Choose the default route for this device.  "vpn_fallback" will continue access through internet if VPN is down. </li>
			<br><b>Address</b> - The IP address assigned to the device.  You can click in this field and change the IP address. </li>
			<br><b>MAC Address</b> - The hardware address of the unit. This is hardcoded into the device. </li>
			<br><b>Name</b> - The name of the device. You can click in this field and change the name.  </li>
			<br><b>Lease Ends</b>- The time when the lease expires. </li>
		</div>
	</div>
</div>
<p>
	<div id='footer'>Copyright © 2014 Sabai Technology, LLC</div>
</p>
</form>

<script type='text/ecmascript'>
var settings;
<?php exec('sh /www/bin/dhcp.sh get') ?>

	$.widget("jai.devicelist", {

  //Adding to the built-in widget constructor method - do this when widget is instantiated
  _create: function(){
    //TO DO: check to see if containing element has a unique id
    
    // BUILDING DOM ELEMENTS
    $(this.element)
    .prepend( $(document.createElement('table'))
    	.addClass("listTable")
    	.prop("id","list") 
    	.width("100%")
    	)

    $('#list').dataTable({
    	'bPaginate': false,
    	'bInfo': false,
    	'bFilter': false,
    	"sAjaxSource": 'libs/data/dhcp.json',
    	"aoColumns": [
    	{ "sTitle": "Make Static", "mData":"static", 'sClass':'staticDrop' },
      { "sTitle": "Route", "mData":"route", 'sClass':'routeDrop' },
    	{ "sTitle": "Address", "mData":"ip", 'sClass':'plainText'  },
    	{ "sTitle": "MAC", "mData":"mac" },
    	{ "sTitle": "Name", "mData":"name", "sClass":"plainText"},
    	{ "sTitle": "Lease Ends", "mData":"time" }
    	],

    	'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull){
    		$(nRow).find('.plainText').editable(
    			function(value, settings){ return value; },
    			{
    				'onblur':'submit',
    				'event': 'click',
    				'placeholder' : 'Click to edit'
    			}
    			);

        $(nRow).find('.staticDrop').editable(
          function(value, settings){ return value; },
          {
          'data': " {'on':'on','off':'off'}",
          'type':'select',
          'onblur':'submit',
          'placeholder':'off',
          'event': 'click'
          }
          );

          $(nRow).find('.routeDrop').editable(
          function(value, settings){ return value; },
          {
          'data': " {'default':'default','internet':'internet','vpn_fallback':'vpn_fallback','vpn_only':'vpn_only','accelerator':'accelerator'}",
          'type':'select',
          'onblur':'submit',
          'placeholder':'default',
          'event': 'click'
          }
          );

    	} /* end fnRowCallback*/
    })

    this._super();
}
});

$(function(){
  //instatiate widgets on document ready
  $('#devicelist').devicelist();
})

  var hidden, hide,res;
  var f = E('fe'); 
  var hidden = E('hideme'); 
  var hide = E('hiddentext');

function DHCPcall(act){ 
  $('#list').blur();
    E("act").value=act;
    if ( act = "save") {
      //Save any Static DHCP
      STATICcall();
      //splash UI message
      hideUi("Adjusting DHCP settings..."); 
     //read the text values
     var TableData=new Array();
     $('#list tr').each(function(row, tr){
      TableData[row] = {
          "static" : $(tr).find('td:eq(0)').text()
        , "route" : $(tr).find('td:eq(1)').text()
        , "ip" : $(tr).find('td:eq(2)').text()
        , "mac" : $(tr).find('td:eq(3)').text()
        , "name" : $(tr).find('td:eq(4)').text()
        , "time" : $(tr).find('td:eq(5)').text()
      };
    });
     //create json data from table on screen
     TableData = $.toJSON(TableData);
      var json=$.parseJSON(TableData);
      $("#dhcptable").val(TableData);
   };

// Pass the form values to the php file 
$.post('php/dhcp.php', $("#fe").serialize(), function(pass){
  res=$.parseJSON(pass);
// Detect if values have been passed back   
if( res.rMessage != ""){
  PORTresp();
};
showUi();
});
// Important stops the page refreshing
return false;
} 


function PORTresp(){ 
  msg(res.rMessage); 
  showUi(); 
} 

function STATICcall(){ 
  var datatable = $('#list').DataTable();
  datatable
    .rows(':has(:checkbox:checked)')
    .draw();
//$('#rowclick2 tr').filter(':has(:checkbox:checked)').find('td');
} 

function REFcall(){ 
$(function(){
    <?php exec('sh /www/bin/dhcp.sh') ?>
  //refresh widgets on document 
  $('#devicelist').devicelist();
})
};

</script>
