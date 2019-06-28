<?php
require 'core.php';
//CURRENT OBS
$url = 'https://api.weather.com/v2/pws/observations/current?stationId=IGARGNAN4&format=json&units=m&apiKey=81f69e3da6b04689b69e3da6b096898b';
$json = file_get_contents($url);
$obj = json_decode($json);

//api retrieval
$wx = $obj->observations[0];

$sw = $wx->softwareType;
$nome = $wx->neighborhood;
$obstime = $wx->obsTimeLocal;

$wm2 = $wx->solarRadiation;
$uv = $wx->uv;
$winddir = $wx->winddir;
$humidity = $wx->humidity;

$current = $wx->metric;

$temp = $current->temp;
$hi = $current->heatIndex;
$dewpoint = $current->dewpt;
$wspd = $current->windSpeed;
$gusts = $current->windGust;
$press = $current->pressure;

$calore = getHeatInfo($hi);

//upcoming

$url1 = 'https://api.weatherbit.io/v2.0/forecast/hourly?city=Gargnano&country=IT&key=607265bbae2d428d902a71395af79635&hours=24';
$txt = file_get_contents($url1);
$json_fcst = json_decode($txt, true);

//qualita Aria
$url2 = 'https://api.weatherbit.io/v2.0/current/airquality?city=Gargnano&country=IT&key=607265bbae2d428d902a71395af79635';
$txt = file_get_contents($url2);
$json_q = json_decode($txt, true);

?>

<html lang="it" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Wx Indexes</title>
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
            <td>Indice di calore</td>
            <td style="background-color: <?php echo $calore['color'] ?>;">
              + <?= $hi ?> °C<br>
              <?php echo $calore['msg'] ?><br>
              <?php echo $humidity?>% di umidit&agrave;
            </td>
          </tr>
          <tr>
            <td>Pressione di vapore</td>
            <td>Satura: <?php echo getVapPressure($temp) ?> mbar<br>
              Effettiva: <?php echo getVapPressure($dewpoint) ?> mbar</td>
            </tr>
            <tr>
              <td>Vento</td>
              <td>da <?php echo ceil($winddir / 10) * 10; ?>°<br>
                min. <?php echo $wspd ?> km/h<br>
                max. <?php echo $gusts ?> km/h
              </td>
            </tr>
            <tr>
              <td>Pressione Atmosferica</td>
              <td>QFE - <?php echo floor($press) ?> hPa<br>
                QNH - <?php echo convertQFEToQNH($press) ?> hPa
              </td>
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
              <td style="background-color: <?php echo getColorUV($uv)['color']; ?>;"><?php echo $uv ?><br>
                <small><?php echo getColorUV($uv)['msg']; ?></small>
              </td>
            </tr>
            <tr>
              <td>Energia Solare attuale</td>
              <td><?php echo round($wm2/1000, 1) ?> KWh/mq<br>(<?php echo floor(convertWm2toLux($wm2)); ?> lux @ 555 nm)</td>
            </tr>
            <tr>
              <td class="title" colspan="2">Qualit&agrave; dell'atmosfera</td>
            </tr>
            <tr>
              <td>O<sub>3</sub> (Ozono)</td><td style="background-color: <?php echo getOzono($json_q['data'][0]['o3'])['color'] ?>;"><?php echo floor($json_q['data'][0]['o3']); ?> µg/m<sup>3</sup><br>
                (<?php echo getOzono($json_q['data'][0]['o3'])['msg'] ?>)</td>
            </tr>
              <tr>
                <td>PM2.5<br><small>(pericolo >25µg/m<sup>3</sup>)</small></td> <td><?php echo floor($json_q['data'][0]['pm25']); ?> µg/m<sup>3</sup></td>
              </tr>
              <tr>
                <td>PM10<br><small>(pericolo >50µg/m<sup>3</sup>)</small></td> <td><?php echo floor($json_q['data'][0]['pm10']); ?> µg/m<sup>3</sup></td>
              </tr>
            </table>
          </div>

          <div class="col-md-4">
            <table class="table table-dark">
              <tr>
                <td class="title">
                  RADAR Valeggio sul Mincio<hr>
                  <img width="500px" height="500px" src="http://www.arpa.veneto.it/previsioni/radar_valeggio/PPI_36_Z_2_01.PNG">
                </td>
              </tr>
              <!--<tr>
              <td class="title">
              Webcam Bogliaco<hr>
              <img width="500px" height="500px" src="http://www.marinadibogliaco.com/meteo/bogliaco.jpg?n=1678601804">
            </td>
          </tr>-->
        </table>
      </div>
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
              echo "<td>Ore ".substr(explode("T", $json_fcst['data'][$i]['timestamp_local'])[1], 0, 2)."</td>";
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
        </table>
      </div>
    </div>
</div>

</body>
<script src="script.js" charset="utf-8"></script>
</html>
