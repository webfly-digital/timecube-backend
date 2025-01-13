<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Полировочные работы");
?>

    <div class="three-columns" id="inner-page">
        <section class="three-columns__body">
            <div class="container-fluid breadcrumbs-wrapper">
                <?
                $APPLICATION->IncludeComponent("bitrix:breadcrumb","catalog",
                    ["START_FROM" => "0","PATH" => "","SITE_ID" => "s1"],
                    false,["HIDE_ICONS" => "Y"]
                );
                ?>
            </div>
            <div class="container-fluid">
                <div class="heading">
                    <div class="heading__item">
                        <h1 class="heading__title"><?=$APPLICATION->ShowTitle(true)?></h1>
                    </div>
                </div>
                <div class="text-content">
                    <p>Полировочные работы производятся с применением только профессионального оборудования и профессиональных полировочных материалов компании 3М.</p>
                    <div class="alert alert-warning">
                        <p>ВНИМАНИЕ: Полировочные работы производятся при личной явке Вас с изделием в Сервис центр для оценки изношенности лакокрасочного покрытия и возможности полировки.</p>
                    </div>
                    <p>Предварительно звоните: 8 (495) 687-35-18.</p>

                    <p class="h3">Стоимость полировочных работ</p>
                    <div class="media-card-h">
                        <div class="media-card-h__pic"><img class="lozad" alt="Стоимость полировочных работ Малый размер" title="Стоимость полировочных работ Малый размер" data-src="/service/images/cube-sm.png"></div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title">Малый размер (категория А)</p>
                            <p>Размер шкатулки: 15 х 15 см</p>
                            <p><a href="#" onclick="jivo_api.open({start : 'call'}); return false;">Заказать полировку</a></p>
                        </div>
                    </div>
                    <div class="media-card-h">
                        <div class="media-card-h__pic"><img class="lozad" alt="Стоимость полировочных работ Малый размер" title="Стоимость полировочных работ Средний размер"  data-src="/service/images/cube-md.png"></div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title">Средний размер (категория Б)</p>
                            <p>Размер шкатулки: 25 х 25 см</p>
                            <p><a href="#" onclick="jivo_api.open({start : 'call'}); return false;">Заказать полировку</a></p>
                        </div>
                    </div>
                    <div class="media-card-h">
                        <div class="media-card-h__pic"><img class="lozad" alt="Стоимость полировочных работ Малый размер" title="Стоимость полировочных работ Больше среднего "  data-src="/service/images/cube-lg.png"></div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title">Больше среднего (категория В)</p>
                            <p>Размер шкатулки: 30 х 50 см</p>
                            <p><a href="#" onclick="jivo_api.open({start : 'call'}); return false;">Заказать полировку</a></p>
                        </div>
                    </div>
                    <div class="media-card-h">
                        <div class="media-card-h__pic"><img class="lozad" alt="Стоимость полировочных работ Малый размер" title="Стоимость полировочных работ Большой размер"  data-src="/service/images/cube-xl.png"></div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title">Большой размер (категория Г):</p>
                            <p>Размер шкатулки: 70 х 60 см</p>
                            <p><a href="#" onclick="jivo_api.open({start : 'call'}); return false;">Заказать полировку</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">

<div class="white-content"> <article class="service-item"> 
    <div class="service-item-content clearfix"> 
      <div class="sic-text"> 
        <p>Полировочные работы производятся 
          <br />
         с применением только профессионального оборудования и профессиональных полировочных материалов компании 3М.</p>
       
        <p><span>ВНИМАНИЕ:</span> Полировочные работы производятся при личной явке Вас с изделием в <a href="#" >Сервис центр</a> для оценки изношенности лакокрасочного покрытия и возможности полировки. 
          <br />
         <strong>Предварительно звоните: <b>8 (495) 687-35-18</b>.</strong></p>
       </div>
     
      <div class="sic-img-box"><img  alt="Полировочные работы производятся с применением только профессионального оборудования и профессиональных полировочных материалов компании 3М" title="Полировочные работы производятся с применением только профессионального оборудования и профессиональных полировочных материалов компании 3М"  src="/service/images/polirovka.jpg"  /></div>
     </div>
   </article> </div>
 
<div class="white-content serv-list-grig"> 
  <h2>Стоимость полировочных работ:</h2>
 
  <ul class="serv-list"> 
    <li class="color-1"> 
      <h3>Малый размер (категория А):</h3>
     
      <div class="size">Размер шкатулки: 15 х 15 см</div>
     
      <div class="img-box"><img alt="Стоимость полировочных работ Малый размер 1000 руб" title="Стоимость полировочных работ Малый размер 1000 руб"   src="/service/images/cube-sm.png"  /></div>
     
      <div class="price"><span>1000</span> руб.</div>
     
      <div class="button-box"><a class="order-polish" href="#!" >Заказать полировку</a></div>
     </li>
   
    <li class="color-2"> 
      <h3>Средний размер (категория Б):</h3>
     
      <div class="size">Размер шкатулки: 25 х 25 см</div>
     
      <div class="img-box"><img src="/service/images/cube-md.png" alt="Стоимость полировочных работ Средний размер 2000 руб" title="Стоимость полировочных работ Средний размер 2000 руб"  /></div>
     
      <div class="price"><span>2000</span> руб.</div>
     
      <div class="button-box"><a class="order-polish" href="#!" >Заказать полировку</a></div>
     </li>
   
    <li class="color-3"> 
      <h3>Больше среднего (категория В):</h3>
     
      <div class="size">Размер шкатулки: 30 х 50 см</div>
     
      <div class="img-box"><img src="/service/images/cube-lg.png" alt="Стоимость полировочных работ Больше среднего 3000 руб" title="Стоимость полировочных работ Больше среднего 3000 руб"  /></div>
     
      <div class="price"><span>3000</span> руб.</div>
     
      <div class="button-box"><a class="order-polish" href="#!" >Заказать полировку</a></div>
     </li>
   
    <li class="color-4"> 
      <h3>Большой размер (категория Г):</h3>
     
      <div class="size">Размер шкатулки: 70 х 60 см</div>
     
      <div class="img-box"><img src="/service/images/cube-xl.png" alt="Стоимость полировочных работ Большой размер 4000 руб" title="Стоимость полировочных работ Большой размер 4000 руб"  /></div>
     
      <div class="price"><span>4000</span> руб.</div>
     
      <div class="button-box"><a class="order-polish" href="#!" >Заказать полировку</a></div>
     </li>
   </ul>
 </div>


    </div>
    </section>
        <?$APPLICATION->IncludeComponent("bitrix:main.include","",
            ["AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        );?>
    </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>