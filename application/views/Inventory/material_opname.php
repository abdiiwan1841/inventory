<?php
$zfm=new zetro_frmBuilder('asset/bin/zetro_inv.frm');
$zlb=new zetro_buildlist();
$zlb->config_file('asset/bin/zetro_inv.frm');
$path='application/views/inventory';
//$printer="<img src='".base_url()."asset/images/print.png' id='printsheet' title='Print count sheet'>";
link_css('autosuggest.css','asset/css');
link_js('material_opname.js,auto_sugest.js,jquery_terbilang.js,jquery.fixedheader.js',$path.'/js,asset/js,asset/js,asset/js');
panel_begin('Stock Opname',false,'',($p_countsheet!='')?'Print to printer':'');
panel_multi('countsheet','block');
	echo "<form id='frm2' name='frm2' method='post' action=''>";
	addText(array('Kategori','Order By','Urutan',''),
			array("<select id='Kategori' name='Kategori'></select>",
				  "<select id='orderby' name='orderby'>".selectTxt('SusunanStock')."</select>",
				  "<select id='urutan' name='urutan'>".selectTxt('Urutan',true)."</select>",
				  "<input type='button' value='OK' id='okelah'/>"));
	echo "</form>";
		$zlb->section('stoklistview');
		$zlb->aksi(false);
		$zlb->Header('100%','sheet');
		echo "</tbody></table>";
panel_multi_end();
panel_multi('stockadjustment','none',false);
if($c_adjust!='' || $e_adjust!=''){
	echo "Select Filter of list :";
	$zfm->AddBarisKosong();
	$zfm->Start_form(true,'frm1');
	$zfm->BuildForm('stoklist filter',true,'50%');
	$zfm->BuildFormButton('Process','filter','button',1);
	echo "<hr>";
		$zlb->inlineedit('y');
		$zlb->section('stoklistview');
		$zlb->aksi(true);
		$zlb->Header('100%');
		echo "</tbody></table>";
}else{ no_auth();}
panel_multi_end();
panel_multi('stockbalance');
panel_multi_end();
panel_end();
auto_sugest();
tab_select('prs');
?>
<script language="javascript">
$(document).ready(function(e) {
   $('#Kategori').html("<? dropdown('inv_barang_kategori','ID','Kategori','order by Kategori','8');?>")
    $('#p-0').click(function(){
		$('#frm2').attr('action','countsheet_prn');
		document.frm2.submit();
	})
});
</script>