<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//class member

class Member extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model("Admin_model");
		$this->load->model("member_model");
		$this->load->model('inv_model');
		$this->load->helper('print_report');	
		$this->load->library("zetro_auth");
		$this->userid=$this->session->userdata('idlevel');
	}
	function Header(){
		$this->load->view('admin/header');	
	}
	
	function Footer(){
		$this->load->view('admin/footer');	
	}
	function list_data($data){
		$this->data=$data;
	}
	function View($view){
		$this->Header();
		$this->load->view($view,$this->data);	
		$this->Footer();
	}
	function index(){
		$this->zetro_auth->menu_id(array('pelangganbaru','uploadphoto'));
		$this->list_data($this->zetro_auth->auth());
		$this->View('member/member_view');
	}
	function member_list(){
        $datax=array();
		empty($_POST['id_dept'])?$where='':$where="where ID_Dept='".$_POST['id_dept']."'";
		empty($_POST['ordby'])? $ordby='order by noUrut':$ordby='order by '.$_POST['ordby'];
		$data['datax']=$this->get_pelanggan();
		$this->zetro_auth->menu_id(array('member__member_list'));//array('list'),array($datax))
		$this->list_data(array_merge($this->zetro_auth->auth(),$data));
		$this->View('member/member_list');
	}
    function get_pelanggan()
	{
		$data=array();$nasabah='';
		//$data=$this->Admin_model->show_list("mst_anggota","where nama is not null and length(nama)>1 group by Nama,Alamat order by nama");
        $data=$this->Admin_model->show_list("mst_anggota_view","where  length(Nama)>1 group by Nama order by Nama");
		foreach($data as $r)
		{
            //$akuns.="&quot;".str_replace("\""," ",strtoupper($r->Nama_Barang))."&quot;,";
			$nasabah.="&quot;".str_replace("\\","-",str_replace("\""," ",strtoupper($r->Nama)))."&quot;,";
		}
		return substr($nasabah,0,-1);
	}
	function filter_by(){
		$datax=array();$n=0;
		//(empty($_POST['id_dept'])||$_POST['id_dept']=='all')?$where="where id_jenis='1'":$where="where ID_Jenis='1' and ID_Dept='".$_POST['id_dept']."'";
		//(empty($_POST['ordby']) || $_POST['ordby']=='undefined')? $ordby='order by noUrut':$ordby='order by '.$_POST['ordby'];
		//(empty($_POST['stat'])||$_POST['stat']=='all')? $where .='':
		//			 $where .=" and id_Aktif='".$_POST['stat']."'";
        $nama=explode('-',@$_POST['searchby']);
        $nam=(count($nama)>0)?$nama[0]:'';
        $where=" where LENGTH(Nama)>1 ";// and ID_Jenis<>'5'";
		$where.= empty($_POST['searchby'])? '':" and Nama like '".trim($nam)."%'";
        $group= empty($_POST['nama'])?'':$_POST['nama'];
        switch($group)
        {
            case 'all':
            $where.="";
            break;
            case 'umum':
            $where.=" and ID_Check !='Y'";
            break;
            case 'lpg':
            $where.=" and ID_Check='Y'";
            break;
        }
        $where.=" order by Nama";
        $datax=$this->Admin_model->show_list("mst_anggota_view",$where);
		//$datax=$this->Admin_model->show_list('mst_anggota',$where);
		
		if(count($datax)>0){
			foreach($datax as $row){
			$n++;
			echo tr().td($n,'center').
					  td($row->No_Agt,'center').
					  td($row->Nama).
					  //td($row->Catatan).
					  td(ucwords($row->Alamat." ".$row->Kota)).
					  td($row->Telepon.", ".$row->Faksimili).
                      td(($row->ID_Check!='Y')?'':$row->ID_Check,'center').
					  td(($row->ID_Check=='Y')?number_format($row->ID_Kelamin,2):0,'right').
					  td(img_aksi($row->ID,false,''),'center').
					_tr();
			}
		}else{
			echo "<tr><td colspan='9' class='kotak'>
			<img src='".base_url()."asset/images/16/warning_16.png'> &nbsp;Nama Pelanggan tidak ada di database </td></tr>";
		}
			//echo $data['list']=count($datax);
	}
    function filterby(){
		$datax=array();$n=0;
		$where =empty($_POST['stat'])?"where ID_Aktif=0":"where ID_Aktif='".$_POST['stat']."'";
        $where.=empty($_POST['searchby'])?'':" and Nama like '%".$_POST['searchby']."%'";
        $ordby ="order by Nama,ID";
		$datax=$this->Admin_model->show_list('mst_anggota',$where.' '.$ordby);
		//print_r($datax);
		if(count($datax)>0){
			foreach($datax as $row){
			$n++;
                if($row->NIP=='Harga_Jual'){$grp="Umum";}
                else if($row->NIP=='Harga_Cabang'){$grp='Toko';}
                else if($row->NIP=='Harga_Partai'){$grp='Grosir';}
                else{$grp='';}
                
			echo tr().td($n,'center').
					  td($row->No_Agt,'center').
					  td($row->Nama).
					  td($row->Catatan).
					  td($row->Alamat." ".$row->Kota." telp :".$row->Telepon."/ ".$row->Faksimili).
					  td($grp).
					  td(($row->Status>0)?number_format($row->Status,2):0,'right').
					  td(img_aksi($row->ID,true,'edit'),'center').
					_tr();
			}
		}else{
			echo "<tr><td colspan='8' class='kotak'>
			<img src='".base_url()."asset/images/16/warning_16.png'> &nbsp;Name not found in Database, please try again </td></tr>";
		}
			//echo $data['list']=count($datax);
	}
	function set_anggota(){
		//table mst_anggota
		$data=array();
		$data['No_Agt']		=$_POST['No_Agt'];
		$data['NoUrut']		=$_POST['No_Agt'];
		$data['ID']			=empty($_POST['idm'])?0:$_POST['idm'];
		$data['ID_Dept']	='1';
        $data['NIP']        =empty($_POST['NIP'])?'Harga_Jual':$_POST['NIP'];
		$data['Nama']		=addslashes(strtoupper($_POST['Nama']));
		$data['Catatan']	=empty($_POST['Catatan'])?'':addslashes(strtoupper($_POST['Catatan']));
		$data['Alamat']		=empty($_POST['Alamat'])?'':addslashes(ucwords($_POST['Alamat']));
		$data['Kota']		=empty($_POST['Kota'])?'':addslashes(ucwords($_POST['Kota']));
		$data['Propinsi']	=empty($_POST['Propinsi'])?'':addslashes(ucwords($_POST['Propinsi']));
		$data['Telepon']	=empty($_POST['Telepon'])?'':$_POST['Telepon'];
		$data['Faksimili']	=empty($_POST['Faksimili'])?'':$_POST['Faksimili'];
		$data['ID_Aktif']	=empty($_POST['ID_Aktif'])?'0':$_POST['ID_Aktif'];
		$data['ID_Jenis']	='1';
		$data['TanggalMasuk']=empty($_POST['TanggalMasuk'])?date('Ymd'):TglToSql($_POST['TanggalMasuk']);
        $data['ID_Check']        =empty($_POST['plpg'])?'N':$_POST['plpg'];
        $data['NamaPangkalan']=empty($_POST['pangkalan'])?'':$_POST['pangkalan'];
        $data['ID_Kelamin']=empty($_POST['maxlpg'])?'0':$_POST['maxlpg'];
		$data['Status']		=empty($_POST['Status'])?'0':$_POST['Status'];
        $data['Keterangan']=empty($_POST['peruntukan'])?'':$_POST['peruntukan'];
        $data['No_Perkiraan']=empty($_POST['barcode'])?'':$_POST['barcode'];
		$this->Admin_model->replace_data('mst_anggota',$data);
	}
	
	function get_nomor_anggota(){
		echo 'PL-'.$this->member_model->nomor_anggota();	
	}
	function delete_member(){
        $data=array();
        $ID=$_POST['id_member'];
        //$this->Admin_model->upd_data('mst_anggota',"set ID_Aktif='1'","where ID='".$ID."'");
       echo $this->Admin_model->hps_data('mst_anggota',"where ID='".$ID."'");
    }
	function get_anggota(){
		$arr=array();
		$str	=$_GET['str'];
		$limit	=$_GET['limit'];
		$ID_Dept=empty($_GET['dept'])?'':$_GET['dept'];
		$datax=$this->member_model->get_anggota($str,$limit,$ID_Dept);
		echo json_encode($datax);	
	}
	function get_kota(){
		$arr=array();
		$str=$_GET['str'];
		$datax=$this->member_model->get_kota($str);
		echo json_encode($datax);	
	}
	function get_propinsi(){
		$arr=array();
		$str=$_GET['str'];
		$datax=$this->member_model->get_propinsi($str);
		echo json_encode($datax);	
	}
	
	function do_upload()
	{	//upload foto anggota to uploads/member
		$datax=array();
		($this->input->post('NIP')!='')?$nip="(".$this->input->post('NIP').")":$nip="";
		$config['allowed_types'] = 'pdf|gif|jpg|png';
		$config['upload_path'] ='./uploads/member';
		$config['file_name']=str_replace(".",'_',$this->input->post('Nama')).$nip;
		$config['max_size']	= '0';
		$config['max_width']  = '0';
		$config['max_height']  = '0';
		$config['overwrite']=true;
		
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ( ! $this->upload->do_upload('PhotoLink'))
		{
			$this->zetro_auth->menu_id(array('anggotabaru','uploadphoto'));
			$data=$this->zetro_auth->auth(array('upload_data','panel','d_photo','error','nama','nip'),
					array($this->upload->data(),'uploadphoto','block',$this->upload->display_errors(),$this->input->post('Nama'),$this->input->post('NIP')));
			$this->Header();
			$this->load->view('member/member_view', $data);
			$this->Footer();
		}
		else
		{
			$this->zetro_auth->menu_id(array('anggotabaru','uploadphoto'));
			$data=$this->zetro_auth->auth(array('upload_data','panel','d_photo','error','nama','nip','nourut'),
					array($this->upload->data(),'uploadphoto','block',$this->upload->display_errors(),$this->input->post('Nama'),$this->input->post('NIP'),$this->input->post('no_agt')));
			$this->Header();
			$this->load->view('member/member_view', $data);
			$this->Footer();
		}
	}
	function simpan_photo(){
		$no_anggota=$_POST['no_anggota'];
		$photo_anggota=$_POST['photo_anggota'];
		$this->Admin_model->upd_data('mst_anggota',"set PhotoLink='".$photo_anggota."'","where NoUrut='$no_anggota'");
		echo "Tersimpan";
	}
	
	function field_orderby(){
		$data=$this->member_model->show_field('mst_anggota');
		echo json_encode($data);
	}
	function get_nama_simpanan(){
		$data=$this->member_model->jenis_simpanan($_POST['id']);
		echo $data;	
	}
	function member_detail(){
	$data=array();
	$no_anggota				=$_POST['no_anggota'];
	$data['kunci']			=$no_anggota;
	$data['no_anggota']		=$this->Admin_model->show_single_field('mst_anggota','No_Agt',"where ID='".$no_anggota."'");
	$data['nm_anggota']		=$this->Admin_model->show_single_field('mst_anggota','Nama',"where ID='".$no_anggota."'");
	$data['id_department']	=rdb('mst_departemen','Departemen','Departemen',"where ID='".
							$this->Admin_model->show_single_field('mst_anggota','ID_Dept',"where ID='".$no_anggota."'")."'");
	$data['transaksi']=$this->member_model->summary_member_data($no_anggota);
	$this->load->view('member/member_detail',$data);
	}
	function member_detail_trans(){
		$n=0;$total_debet=0;$total_kredit=0;
		$ID_Agt		=$_POST['ID_Agt'];
		$ID_Jenis	=$_POST['ID_Jenis'];
		$data=$this->member_model->detail_member_data($ID_Agt,$ID_Jenis);
		foreach($data->result() as $trn){
			$n++;
			echo "<tr class='xx' align='center'>
				  <td class='kotak'>$n</td>
				  <td class='kotak'>".tglfromSql($trn->Tanggal)."</td>
				  <td class='kotak'>".$trn->Nomor."</td>
				  <td class='kotak' align='left'>".$trn->Keterangan."</td>
				  <td class='kotak' align='right'>".number_format($trn->Debet,2)."</td>
				  <td class='kotak' align='right'>".number_format($trn->Kredit,2)."</td>
				  </tr>";
				  $total_debet +=$trn->Debet;
				  $total_kredit +=$trn->Kredit;
		};
		echo "<tr class='xx'>
			  <td class='kotak list_genap' colspan='4' align='right'>TOTAL &nbsp;&nbsp;</td>
			  <td class='kotak list_genap' align='right'><b>".number_format($total_debet,2)."</b></td>
			  <td class='kotak list_genap' align='right'><b>".number_format($total_kredit,2)."</b></td>
			  </tr>";
	}
	function member_biodata(){
		$datax=array();
		$no_anggota	=$_POST['no_anggota'];
		$data=$this->member_model->biodata_member($no_anggota);
		foreach($data->result() as $rr){
			$datax=$rr;
		}
		echo json_encode($datax);
	}
	//simpanan anggota
	
	function member_saving(){
		$this->zetro_auth->menu_id(array('simpananpokok','simpananwajib','simpanankhusus'));
		$this->list_data($this->zetro_auth->auth());
		$this->View('member/member_simpanan');
	}
	
	function get_member_kredit(){
		$data=array();$n=0;
		$where="where hutang_pelanggan >0";//($_POST['status']=='')?'':"where p.stat_pinjaman='".$_POST['status']."'";
		$where=empty($_POST['cari'])?$where:"where nm_pelanggan like '".$_POST['cari']."%' and hutang_pelanggan >0";
		$orderby=" order by ".$_POST['orderby'];
		$orderby.=empty($_POST['urutan'])? '':' '.$_POST['urutan'];
		//echo $where;
		$data=$this->member_model->get_data_pinjaman($where,$orderby);
		foreach($data as $r){
			$n++;
			echo tr().td($n,'center').
				 td(strtoupper($r->nm_pelanggan)).
				 //td('','right').
				 //td('','right').
				 td(number_format($r->hutang_pelanggan,2),'right').
				 td(tglfromSql($r->doc_date),'center').
				 td('Sudah <b>'.$r->Lama ."</b> Hari",'center').
				_tr();	 
		}
	}
	function get_member_kredit_print(){
		$data=array();$n=0;
		$where=($this->input->post('stat_tag')=='')?'':"where p.stat_pinjaman='".$this->input->post('stat_tag')."'";
		$data=array();$n=0;
		$where="where hutang_pelanggan >0";//($_POST['status']=='')?'':"where p.stat_pinjaman='".$_POST['status']."'";
		$where=empty($_POST['cari'])?$where:"where nm_pelanggan like '".$_POST['cari']."%' and hutang_pelanggan >0";
		$orderby=" order by ".$_POST['orderby'];
		$orderby.=empty($_POST['urutan'])? '':' '.$_POST['urutan'];
		$data['temp_rec']=$this->member_model->get_data_pinjaman($where,$orderby);

		$this->zetro_auth->menu_id(array('trans_beli'));
		$this->list_data($data);
		$this->View("laporan/lap_member_tagihan_print");
	}
    function get_member_detail()
    {
        $data=array();
        //$nama=explode('-',@$_POST['id']);
        $ID_Agt=@$_POST['id'];//(count($nama)>0)?$nama[0]:'';
         $data=$this->member_model->GetDetailAnggota($ID_Agt);
        $datax["Nama"]='-';
        echo ($data)?json_encode($data[0]):json_encode($datax);
    }
    /*
        added on 21-02-2016
        List Barang titipan
    */
    function titipbarang(){
        $data=array();
		$data['nasabah']=$this->get_pelanggan();
		$this->zetro_auth->menu_id(array('titipbarang'));
		$this->list_data(array_merge($this->zetro_auth->auth(),$data));
		$this->View('member/member_titipbarang');
    }
    function ListBarangTitipan(){
        $data=array();$datax=array();$i=0;$total=0;
        $cari=empty($_POST['cariya'])?"":" where Deskripsi like '%".$_POST['cariya']."%'";
        $orderby=empty($_POST['orderby'])?"":" Order by ".$_POST['orderby'];
        $sort=empty($_POST['urutan'])?"":$_POST['urutan'];
        $data=$this->Admin_model->show_list('mst_pelanggan_nitip',$cari.$orderby." ".$sort);
        foreach($data as $r){
            $i++;$n=0;
            echo tr('xx list_genap').td($i,'center').
                 td(tglFromSql($r->Tanggal),'center').
                 td(strtoupper($r->Deskripsi),'left').
                 td($r->NoUrut,'center\' colspan=\'2').
                 td().
                 td(number_format($r->total_belanja,2),'right').
                 td().
                 td(/*img_aksi($r->NoUrut,false,'pros')*/'','center').
                _tr();
            $datax=$this->Admin_model->show_list('inv_penjualan_view',"where ID_Jual='".$r->ID."'");
            foreach($datax as $row){
               $n++;
                echo tr().td().td($n,'right').
                     td($row->Nama_Barang).
                     td($row->Satuan,'center').
                     td(number_format($row->Jumlah,2),'right').
                     td(number_format(($row->Harga),2),'right').
                     td(number_format(($row->Jumlah*$row->Harga),2),'right').
                     td().td().
                    _tr();
                
            }
            $total+=$r->total_belanja;
        }
        echo tr().td('<b>Total Harga Barang Titipan</b>','right\' colspan=\'6').td("<b>".number_format($total,2)."</b>",'right').td().td()._tr();
    }
}
?>