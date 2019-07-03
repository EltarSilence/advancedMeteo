<?php
error_reporting(0);
require 'core.php';
//CURRENT OBS
$url = 'https://api.weather.com/v2/pws/observations/current?stationId=IGARGNAN4&format=json&units=m&apiKey=81f69e3da6b04689b69e3da6b096898b';
$url_rain = 'https://api.weather.com/v2/pws/observations/current?stationId=ITOSCOLA8&format=json&units=m&apiKey=81f69e3da6b04689b69e3da6b096898b';
$url1 = 'https://api.weatherbit.io/v2.0/forecast/hourly?city=Gargnano&country=IT&key=607265bbae2d428d902a71395af79635&hours=24';
$url2 = 'https://api.weatherbit.io/v2.0/current/airquality?city=Gargnano&country=IT&key=607265bbae2d428d902a71395af79635';

$RECORD_ON_DATABASE = true;

//rain
$txt = file_get_contents($url_rain);
$json_rain = json_decode($txt, true);
$mmh = $json_rain->observations[0]['metric']['precipRate'];
if ($mmh == 0){
  $mmh = 0; //bug
}

try {
  $json = file_get_contents($url);
  $obj = json_decode($json);

  //api retrieval
  $wx = $obj->observations[0];

  $sw = $wx->softwareType;
  $nome = $wx->neighborhood;
  $obstime = $wx->obsTimeLocal;

  $wm2 = $wx->solarRadiation;
  if (is_null($wm2)) {
    $wm2 = 'N/D';
    $lux = 'N/D';
  }
  else {
    $lux = floor(convertWm2toLux($wm2));
  }
  $uv = $wx->uv;
  if (is_null($uv)) $uv = 'N/D';
  $winddir = $wx->winddir;
  if (is_null($winddir)) $winddir = 'N/D';
  $humidity = $wx->humidity;
  if (is_null($humidity)) $humidity = 'N/D';

  $current = $wx->metric;

  $temp = $current->temp;
  if (is_null($temp)) $temp = 'N/D';
  $hi = $current->heatIndex;
  if (is_null($hi)){
    $hi = 'N/D';
    $calore = 'N/D';
  }
  else {
    $calore = getHeatInfo($hi);
  }
  $dewpoint = $current->dewpt;
  if (is_null($dewpoint)) $dewpoint = 'N/D';

  $wspd = $current->windSpeed;
  $gusts = $current->windGust;
  if (is_null($wspd) || is_null($gusts)) {
    $wspd = 'N/D';
    $gusts = 'N/D';
    $beaufort = NULL;
  }
  else {
    $beaufort = getBeaufort(avg($wspd, $gusts))['lvl'];
  }

  $press = $current->pressure;
  if (is_null($press)) $press = 'N/D';

  $qnh = convertQFEToQNH($press);



  //upcoming
  $txt = file_get_contents($url1);
  $json_fcst = json_decode($txt, true);

  //qualita Aria
  $txt = file_get_contents($url2);
  $json_q = json_decode($txt, true);
}
catch(Exception $e) {
  echo 'Errore: ' .$e->getMessage();
}
?>

<html lang="it" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Wx Indexes</title>
  <meta http-equiv="refresh" content="120">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-4">
        <table class="table table-dark">
          <tr>
            <td class="title" colspan="2">Dati climatologici (<?php echo $obstime; ?>)</td>
          </tr>
          <tr>
            <td>Temperatura dell'aria</td>
            <td><?= $temp ?> °C</td>
          </tr>
          <tr>
            <td>Punto di rugiada</td>
            <td><?= $dewpoint ?> °C</td>
          </tr>
          <tr>
            <td>Indice di calore<br><small>(Temperatura percepita)</small></td>
            <td style="background-color: <?php echo $calore['color'] ?>;">
              + <?= $hi ?> °C<br>
              <small><?php echo $calore['msg'] ?></small><br>
              <?php echo $humidity?>% di umidit&agrave;
            </td>
          </tr>
            <tr>
              <td>Vento<br>
                Beaufort: <?php echo '<b>'.$beaufort.'</b> <small>('.getBeaufort(avg($wspd, $gusts))['descr'].')</small>'; ?><br>
                <?php echo 'Lago: <b>'.getBeaufort(avg($wspd, $gusts))['lago'].'</b>'; ?>
              </td>
              <td>da <?php echo ceil($winddir / 10) * 10; ?>°<br>
                min. <?php echo $wspd ?> km/h<br>
                max. <?php echo $gusts ?> km/h
              </td>
            </tr>
            <tr>
              <td>Pressione Atmosferica</td>
              <td>QFE - <?php echo floor($press) ?> hPa<br>
                QNH - <?php echo $qnh ?> hPa
              </td>
            </tr>
            <tr>
              <td>Intensit&agrave; precipitazioni</td>
              <td><?php echo $mmh ?> mm/h</td>
              </tr>
          </table>
        </div>

        <div class="col-md-4">
          <table class="table table-dark">
            <tr>
              <td class="title" colspan="2">Dati meteorologici</td>
            </tr>
            <tr>
              <td>Temperatura Equivalente Potenziale</td>
              <td><?php echo round(TEQ($temp, $dewpoint, $press), 2) ?> °C</td>
            </tr>
            <tr>
              <td>Altezza di formazione basi nubi cumuliformi</td>
              <td><?php $hgt = estimateHeightCu($temp, $dewpoint); echo $hgt ?> m (<?php echo $hgt * 3.28 ?> ft)</td>
            </tr>
            <tr>
              <td>Indice UV</td>
              <td style="background-color: <?php echo getColorUV($uv)['color']; ?>; color: black"><b><?php echo $uv; ?></b><br>
                <small><?php echo getColorUV($uv)['msg']; ?></small>
              </td>
            </tr>
            <tr>
              <td>Energia Solare attuale</td>
              <td><?php echo round($wm2, 2) ?> Wh/mq<br><small>(<?php echo $lux; ?> lux @ spettro 555 nm)</small></td>
            </tr>
          </table>
        </div>

        <?php
        $o3 = $json_q['data'][0]['o3'];
        $pm25 = $json_q['data'][0]['pm25'];
        $pm10 = $json_q['data'][0]['pm10'];
        $no2 = $json_q['data'][0]['no2'];
        $co = $json_q['data'][0]['co'];

        ?>
        <div class="col-md-4">
          <table class="table table-dark">
            <tr>
              <td class="title" colspan="2">Qualit&agrave; dell'atmosfera</td>
            </tr>
            <tr>
              <td>O<sub>3</sub> (Ozono)</td><td style="background-color: <?php echo getOzono($o3)['color'] ?>;"><?php echo floor($o3); ?> µg/m<sup>3</sup><br>
                <small>(Quantit&agrave; <?php echo getOzono($o3)['msg'] ?>)</small></td>
              </tr>
              <tr>
                <td>PM2.5<br><small>(pericolo >25µg/m<sup>3</sup>)</small></td> <td><?php echo floor($pm25); ?> µg/m<sup>3</sup></td>
              </tr>
              <tr>
                <td>PM10<br><small>(pericolo >50µg/m<sup>3</sup>)</small></td> <td><?php echo floor($pm10); ?> µg/m<sup>3</sup></td>
              </tr>
              <tr>
                <td>NO<sub>2</sub><br><small>(pericolo >35µg/m<sup>3</sup>)</small></td> <td><?php echo round($no2,2); ?> µg/m<sup>3</sup></td>
              </tr>
              <tr>
                <td>CO<br><small>(pericolo >10ppm)</small></td> <td><?php echo round(($co)/1000); ?> ppm</td>
              </tr>
            </table>
          </div>

          <!--<div class="col-md-4">
          <table class="table table-dark">
          <tr>
          <td class="title">
          RADAR Valeggio sul Mincio<hr>
          <img width="500px" height="500px" src="http://www.arpa.veneto.it/previsioni/radar_valeggio/PPI_36_Z_2_01.PNG">
        </td>
      </tr>
      <tr>
      <td class="title">
      Webcam Bogliaco<hr>
      <img width="500px" height="500px" src="http://www.marinadibogliaco.com/meteo/bogliaco.jpg?n=1678601804">
    </td>
  </tr>
</table>
</div>-->
</div>

<div class="row">
  <div class="col-md-12">
    <table class="table table-dark">
      <tr>
        <td class="title" colspan="9">Prossime 8 ore</td>
      </tr>
      <tr> <td>Ora</td>
        <?php
        for($i=0;$i<8;$i++){
          echo "<td>Ore ".substr(explode("T", $json_fcst['data'][$i]['timestamp_local'])[1], 0, 5)."</td>";
        }
        ?>
      </tr>
      <tr> <td>% precipit.</td>
        <?php
        for($i=0;$i<8;$i++){
          echo '<td style="background-color: '.getColorPop($json_fcst['data'][$i]['pop']).';">'.$json_fcst['data'][$i]['pop'].'%</td>';
        }
        ?>
      </tr>
      <tr> <td>Clima</td>
        <?php
        for($i=0;$i<8;$i++){
          echo '<td><img width="50px" height="50px" src="https://www.weatherbit.io/static/img/icons/'.$json_fcst['data'][$i]['weather']['icon'].'.png"></td>';
        }
        ?>
      </tr>
      <tr> <td>max. Okta</td>
        <?php
        for($i=0;$i<8;$i++){
          echo '<td>'.round((($json_fcst['data'][$i]['clouds_mid'])/100*8)).'</td>';
        }
        ?>
      </tr>
      <tr> <td>Vento <small>(beta)</small></td>
        <?php
        for($i=0;$i<8;$i++){
          echo '<td>'.round($json_fcst['data'][$i]['wind_spd']*3.6).' km/h ('.$json_fcst['data'][$i]['wind_cdir'].')</td>';
        }
        ?>
      </tr>
    </table>
  </div>
</div>
</div>

<div class="row">
  <div class="col-md-12">
    <table class="table table-dark">
      <tr>
        <td class="title" colspan="7">Ore Successive (fino a 24h)</td>
      </tr>
      <tr> <td>Ora</td>
        <?php
        for($i=8;$i<24;$i+=3){
          echo "<td>Ore ".substr(explode("T", $json_fcst['data'][$i]['timestamp_local'])[1], 0, 5)."</td>";
        }
        ?>
      </tr>
      <tr> <td>% precipit.</td>
        <?php
        for($i=8;$i<24;$i+=3){
          echo '<td style="background-color: '.getColorPop($json_fcst['data'][$i]['pop']).';">'.$json_fcst['data'][$i]['pop'].'%</td>';
        }
        ?>
      </tr>
      <tr> <td>Clima</td>
        <?php
        for($i=8;$i<24;$i+=3){
          echo '<td><img width="50px" height="50px" src="https://www.weatherbit.io/static/img/icons/'.$json_fcst['data'][$i]['weather']['icon'].'.png"></td>';
        }
        ?>
      </tr>
      <tr> <td>max. Okta</td>
        <?php
        for($i=8;$i<24;$i+=3){
          echo '<td>'.round((($json_fcst['data'][$i]['clouds_mid'])/100*8)).'</td>';
        }
        ?>
      </tr>
      <tr> <td>Visibilit&agrave; <small>(beta)</small></td>
        <?php
        for($i=8;$i<24;$i+=3){
          echo '<td>'.round(($json_fcst['data'][$i]['vis'])).' km</td>';
        }
        ?>
      </tr>
      <tr> <td>Massima Umidit&agrave; R. <small>(beta)</small></td>
        <?php
        for($i=8;$i<24;$i+=3){
          echo '<td>'.($json_fcst['data'][$i]['rh']).'% ';
          if (isset($json_fcst['data'][$i-1]['rh']) && ($json_fcst['data'][$i-1]['rh'] > $json_fcst['data'][$i]['rh'])){
            echo '<i class="fa fa-arrow-down"></i>';
          }
          else {
            echo '<i class="fa fa-arrow-up"></i>';
          }
          echo '</td>';
        }
        ?>
      </tr>
    </table>
  </div>
</div>
</div>

<?php

if ($RECORD_ON_DATABASE){
  $servername = "localhost";
  $username = "root";
  $password = "";

  $conn = new mysqli($servername, $username, $password, 'currentwx');

  /*$data = [$obstime, $temp, $dewpoint, $hi, $humidity, $winddir, $wspd, $gusts, $beaufort, $press, $qnh, $uv, $wm2, $o3, $pm25, $pm10, $no2, $co, $mmh, $sw];
  foreach ($data as $datum){
    if ($datum=='' || is_null($datum)){
      $datum = NULL;
    }
  }*/

  $sql = "INSERT INTO weather (instant, temp, dewp, hi, rh, dirVen, minVen, maxVen, beauf, qfe, qnh, uv, whmq, o3, pm25, pm10, no2, co, mmh, sw) VALUES ('$obstime', $temp, $dewpoint, $hi, $humidity, $winddir, $wspd, $gusts, $beaufort, $press, $qnh, $uv, $wm2, $o3, $pm25, $pm10, $no2, $co, $mmh, '$sw')";

  $conn->query($sql);
}


?>

</body>
<script src="script.js" charset="utf-8"></script>
</html>
