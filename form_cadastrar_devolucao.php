<?
session_start();
require_once("../xfuncoes.php");
//permissao($login,"183");
titulo("Cadastrar Devolução","1","../style.css");
?>

<script>

 <? formatar_moeda(); ?>

 function verifica1()
 {
  if(document.form1.DEV_EMPRESA.value=="")
  {
   alert("Preencha o campo Empresa!");
   document.form1.DEV_EMPRESA.focus();
   return false;
  }
  if(document.form1.DEV_NF.value=="")
  {
   alert("Preencha o campo Nota Fiscal!");
   document.form1.DEV_NF.focus();
   return false;
  }
  if(document.form1.DEV_DATA_EMISSAO.value=="")
  {
   alert("Preencha o campo Data de Emissão!");
   document.form1.DEV_DATA_EMISSAO.focus();
   return false;
  }
  if(document.form1.DEV_CLIENTE.value=="")
  {
   alert("Preencha o campo Cliente!");
   document.form1.DEV_CLIENTE.focus();
   return false;
  }
  document.form1.DEV_CLIENTE.disabled=false;
 }

 function verifica2()
 {
  for(var i=0;i<20;i++)
  {
   if((document.form1.elements['DEV_ITEM['+ i +']'].value=="") &&
      (document.form1.elements['DEV_VALOR['+ i +']'].value!="") &&
      (document.form1.elements['DEV_QTD['+ i +']'].value!=""))
   {
    alert("Preencha todos os campos corretamente!");
    document.form1.elements['DEV_ITEM['+ i +']'].focus();
    return false;
   }
   if((document.form1.elements['DEV_ITEM['+ i +']'].value!="") &&
      (document.form1.elements['DEV_VALOR['+ i +']'].value=="") &&
      (document.form1.elements['DEV_QTD['+ i +']'].value!=""))
   {
    alert("Preencha todos os campos corretamente!");
    document.form1.elements['DEV_VALOR['+ i +']'].focus();
    return false;
   }
   if((document.form1.elements['DEV_ITEM['+ i +']'].value!="") &&
      (document.form1.elements['DEV_VALOR['+ i +']'].value!="") &&
      (document.form1.elements['DEV_QTD['+ i +']'].value==""))
   {
    alert("Preencha todos os campos corretamente!");
    document.form1.elements['DEV_QTD['+ i +']'].focus();
    return false;
   }
  }
  
  for(var j=0;j<20;j++)
  {
   document.form1.elements['DEV_ITEM['+ j +']'].disabled=false;
  }
 }

</script>

<? include("../up.php"); ?>

<?
     if(isset($SUBMIT2))
     {
      // Verificando se a devolução já foi cadastrada anteriormente
      $sql_existe="select count(*) as existe
                   from ast_tbd_devolucao
                   where DEV_EMPRESA='". $DEV_EMPRESA ."' and
                         DEV_NF='". $DEV_NF ."' and
                         DEV_DATA_EMISSAO='". datadesformatada($DEV_DATA_EMISSAO) ."' and
                         DEV_CLIENTE='". $DEV_CLIENTE ."'";
      if(fr_mysql("existe",$sql_existe)=="0")
      {
       // Inserindo os dados gerais da devolução
       $sql_insert="insert into ast_tbd_devolucao(DEV_EMPRESA,DEV_NF,DEV_DATA_EMISSAO,DEV_CLIENTE,DEV_SOL_USR_LOGIN,
                                                  DEV_SOL_OBSERVACAO,DEV_SOL_DATA,DEV_TOTAL)
                    values('". $DEV_EMPRESA ."','". $DEV_NF ."','". datadesformatada($DEV_DATA_EMISSAO) ."',
                           '". $DEV_CLIENTE ."','". $login ."','". $DEV_SOL_OBSERVACAO ."','". date("Y-m-d H:i:s") ."','". $DEV_TOTAL ."')";
       $res_insert=pg_query($conexao_pg,$sql_insert);

       $sql_id="select DEV_ID
                from ast_tbd_devolucao
                where DEV_EMPRESA='". $DEV_EMPRESA ."' and
                      DEV_NF='". $DEV_NF ."' and
                      DEV_DATA_EMISSAO='". datadesformatada($DEV_DATA_EMISSAO) ."' and
                      DEV_CLIENTE='". $DEV_CLIENTE ."' and
                      DEV_SOL_USR_LOGIN='". $login ."' and
                      DEV_SOL_OBSERVACAO='". $DEV_SOL_OBSERVACAO ."' and
                      DEV_TOTAL='". $DEV_TOTAL ."'";
       $DEV_ID=fr_mysql("dev_id",$sql_id);

       for($i=0;$i<20;$i++)
       {
        if(($DEV_ITEM[$i]!="") and ($DEV_QTD[$i]!="") and ($DEV_VALOR[$i]!=""))
        {
         // Inserindo os itens da nota fiscal de devolução
         $sql_insert="insert into ast_tbd_devolucao_item(DEV_ID,DEV_ITEM,DEV_QTD,DEV_VALOR)
                      values('". $DEV_ID ."','". $DEV_ITEM[$i] ."','". $DEV_QTD[$i] ."','". $DEV_VALOR[$i] ."')";
         //print $sql_insert."<br>";
         $res_insert=pg_query($conexao_pg,$sql_insert);
        }
       }
       //Gerando pendência de IDENTIFICAR MATERIAL para Assitência Tencica
       $sql_log="select * from ast_tbd_devolucao_logins where LOG_TIPO='1'";
       $res=pg_query($conexao_pg,$sql_log);
       while($valor=pg_fetch_array($res))
       {
        //pendencia($valor["LOG_USR_LOGIN"],'55',$DEV_ID,'','');
        pendencia($valor["log_usr_login"],'55',$DEV_ID,'','');
       }
      }
     }

     if(!isset($SUBMIT1))
     {
      print "<form name=\"form1\" method=\"post\" action=\"form_cadastrar_devolucao.php\">\r\n";
       print "<table width=\"100%\" cellspacing=\"1\" cellpadding=\"0\" border=\"0\" align=\"center\">\r\n";
        print "<tr class=\"cor3\">\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"100%\" colspan=\"20\">Cadastro de Devolução</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Empresa</td>\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"75%\" colspan=\"15\">\r\n";
          $sql_emp="select * from sis_tbd_empresas where COD_EMPRESA in('01','04') order by COD_EMPRESA";
          fs_mysql("DEV_EMPRESA","cod_empresa","empresa","bordas",$sql_emp,"","");
         print "</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Nota Fiscal</td>\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"75%\" colspan=\"15\">\r\n";
          print "<input type=\"text\" name=\"DEV_NF\" value=\"\" class=\"bordas\">\r\n";
         print "</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Data de Emissão</td>\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"75%\" colspan=\"15\">\r\n";
          inputdata("form1","DEV_DATA_EMISSAO","");
         print "</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Valor Total da Nota</td>\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"75%\" colspan=\"15\">\r\n";
          print "<input type=\"text\" name=\"DEV_TOTAL\" value=\"\" class=\"bordas\" onKeyPress=\"return(formata_reais(this,'.',',',event))\">\r\n";
         print "</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Cliente</td>\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"75%\" colspan=\"15\">\r\n";
          print "<input type=\"text\" name=\"DEV_CLIENTE\" value=\"\" class=\"bordas\" size=\"10\">\r\n";
          $sql_cli="select trim(cod_cliente) as cod_cliente,trim(nom_cliente) as nom_cliente from clientes where ies_situacao!='C' order by nom_cliente";
          fl_ifx("form1","DEV_CLIENTE","bordas","",$sql_cli,"cod_cliente","nom_cliente","cod_cliente@nom_cliente","Código do Cliente@Nome do Cliente","500");
         print "</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td width=\"100%\" colspan=\"20\" class=\"fonte1\" align=\"center\"><br>\r\n";
          print "<input type=\"submit\" name=\"SUBMIT1\" value=\"  OK  \" class=\"botao\" onClick=\"return verifica1()\">\r\n";
         print "</td>\r\n";
        print "</tr>\r\n";
       print "</table>\r\n";
      print "</form>\r\n";
     }
     
     if(isset($SUBMIT1))
     {
      print "<form name=\"form1\" method=\"post\" action=\"form_cadastrar_devolucao.php\">\r\n";
       print "<table width=\"100%\" cellspacing=\"1\" cellpadding=\"0\" border=\"0\" align=\"center\">\r\n";
        print "<tr class=\"cor3\">\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"100%\" colspan=\"20\">Cadastro de Devolução</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Nota Fiscal</td>\r\n";
         print "<td class=\"fonte10\" align=\"left\" width=\"75%\" colspan=\"15\">". $DEV_NF ."</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Data de Emissão</td>\r\n";
         print "<td class=\"fonte10\" align=\"left\" width=\"75%\" colspan=\"15\">". $DEV_DATA_EMISSAO ."</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"left\" width=\"25%\" colspan=\"5\">&nbsp;Cliente</td>\r\n";
         print "<td class=\"fonte10\" align=\"left\" width=\"75%\" colspan=\"15\">\r\n";
          $sql_cli="select trim(cod_cliente) as cod_cliente,trim(nom_cliente) as nom_cliente from clientes where ies_situacao!='C' and cod_cliente='". $DEV_CLIENTE ."'";
          //print $sql_cli."<br>";
          print $DEV_CLIENTE ." - ". fr_ifx("nom_cliente",$sql_cli);
         print "</td>\r\n";
        print "</tr>\r\n";

        print "<tr class=\"cor1\">\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"60%\" colspan=\"12\">Item</td>\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"20%\" colspan=\"4\">Valor Unit</td>\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"20%\" colspan=\"4\">Quantidade</td>\r\n";
        print "</tr>\r\n";

        for($i=0;$i<30;$i++)
        {
         print "<tr class=\"cor2\">\r\n";
          print "<td class=\"fonte2\" align=\"left\" width=\"60%\" colspan=\"12\">&nbsp;\r\n";
           print "<input type=\"text\" name=\"DEV_ITEM[".$i."]\" value=\"\" class=\"bordas\" size=\"10\">\r\n";
           $sql_item="select trim(cod_item) as cod_item,trim(den_item_reduz) as den_item from item where cod_item like '".$DEV_CLIENTE."-%' and cod_empresa='". $DEV_EMPRESA ."' order by den_item";
           fl_ifx("form1","DEV_ITEM[".$i."]","bordas","",$sql_item,"cod_item","den_item","cod_item@den_item","Código do Item@Descrição do Item","700");
          print "</td>\r\n";
          print "<td class=\"fonte2\" align=\"center\" width=\"20%\" colspan=\"4\">\r\n";
           print "<input type=\"text\" name=\"DEV_VALOR[".$i."]\" value=\"\" class=\"bordas\" onKeyPress=\"return(formata_reais(this,'.',',',event))\">\r\n";
          print "</td>\r\n";
          print "<td class=\"fonte2\" align=\"center\" width=\"20%\" colspan=\"4\">\r\n";
           print "<input type=\"text\" name=\"DEV_QTD[".$i."]\" value=\"\" class=\"bordas\">\r\n";
          print "</td>\r\n";
         print "</tr>\r\n";
        }
        print "<tr class=\"cor3\">\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"100%\" colspan=\"20\">Observação</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td class=\"fonte2\" align=\"center\" width=\"100%\" colspan=\"20\">\r\n";
          print "<textarea name=\"DEV_SOL_OBSERVACAO\" cols=\"80\" rows=\"4\" class=\"bordas\"></textarea>\r\n";
         print "</td>\r\n";
        print "</tr>\r\n";
        print "<tr class=\"cor2\">\r\n";
         print "<td width=\"100%\" colspan=\"20\" class=\"fonte1\" align=\"center\"><br>\r\n";
          print "<input type=\"hidden\" name=\"DEV_EMPRESA\" value=\"". $DEV_EMPRESA ."\">\r\n";
          print "<input type=\"hidden\" name=\"DEV_NF\" value=\"". $DEV_NF ."\">\r\n";
          print "<input type=\"hidden\" name=\"DEV_TOTAL\" value=\"". $DEV_TOTAL ."\">\r\n";
          print "<input type=\"hidden\" name=\"DEV_DATA_EMISSAO\" value=\"". $DEV_DATA_EMISSAO ."\">\r\n";
          print "<input type=\"hidden\" name=\"DEV_CLIENTE\" value=\"". $DEV_CLIENTE ."\">\r\n";
          print "<input type=\"submit\" name=\"SUBMIT2\" value=\"  OK  \" class=\"botao\" onClick=\"return verifica2()\">\r\n";
         print "</td>\r\n";
        print "</tr>\r\n";
       print "</table>\r\n";
      print "</form>\r\n";
     }
     if(isset($SUBMIT2))
     {
      print "<div align=\"center\" class=\"fonte4\">Cadastro de Devolução(".$DEV_ID.") efetuado com sucesso!</div>\r\n";
     }
?>

<? include("../down.php"); ?>
