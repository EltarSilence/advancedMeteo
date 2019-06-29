<?php

  function getHeatInfo($hi){
    //input heat index
    $arr = [];

    if ($hi <= 29){
      $msg = "Fuori pericolo";
      $col = "#3cc28c";
    }
    if ($hi>29 && $hi<=34){
      $msg = "Pericolo relativamente basso";
      $col = "#3cc2b7";
    }
    if ($hi>34 && $hi<=39){
      $msg = "Pericolo moderato. Evitare lunghe esposizioni.";
      $col = "#e5f01d";
    }
    if ($hi>39 && $hi<=45){
      $msg = "Pericolo elevato. Evitare sforzi.";
      $col = "#fcaa05";
    }
    if ($hi>45 && $hi<=50){
      $msg = "Pericolo molto elevato. Non esporsi al sole.";
      $col = "#f24805";
    }
    if ($hi >50){
      $msg = "Situazione estrema. Pericolo di morte";
      $col = "#ea05f2";
    }
    $arr['msg'] = $msg;
    $arr['color'] = $col;

    return $arr;

  }

  function getVapPressure($t){
    $t+=237.15;
    return round(6.11 * pow(10, (7.5*$t)/(237.7+$t)), 2);
  }

  function TEQ($tmp, $dwp, $prs) {
    $A = 19.0785;
    $B = 4098.025;
    $C = 237.3;
    $F = 18 / 28.96;
    return 7.4131 * pow(1 / $prs, 0.29) * 2480 * pow(M_E, $A - $B / ($C + $dwp)) / $prs - pow(M_E, $A - $B / ($C + $dwp)) + $tmp + 273.15;
  }

  function estimateHeightCu($t, $d){
    return round(125 * ($t-$d));
  }

  function getColorUV($uv){

    $arr = [];

    if ($uv < 1){
      $arr['color'] = '#a4a8a3';
      $arr['msg'] = 'Intensità dei raggi solari quasi nulla, nessun danno alla pelle, esposizione 100% sicura';
    }
    if ($uv >=1 && $uv <3){
      $arr['color'] = '#4bcc56';
      $arr['msg'] = 'Intensità dei raggi solari ridotta, nessun danno alla pelle, esposizione sicura.';
    }
    if ($uv >=3 && $uv <5){
      $arr['color'] = '#f3fa25';
      $arr['msg'] = 'Intensità dei raggi solari moderata, esposizione abbastanza sicura.';
    }
    if ($uv >=5 && $uv <7){
      $arr['color'] = '#ffb300';
      $arr['msg'] = 'Intensità dei raggi solari elevata. Esposizione rischiosa per le pelli chiare o non troppo scure.';
    }
    if ($uv >=7 && $uv <9){
      $arr['color'] = '#ff2200';
      $arr['msg'] = 'Intensità dei raggi solari molto elevata. Esposizione molto pericolosa.';
    }
    if ($uv >=9){
      $arr['color'] = '#ff00d4';
      $arr['msg'] = 'Intensità dei raggi solari estrema.';
    }

    return $arr;
  }

  function convertQFEToQNH($qfe){
    $qnh = $qfe + 668/27;
    return floor($qnh);
  }

  function convertWm2toLux($wm2){
    return 685*$wm2;
  }

  function getColorPop($pop){
    if ($pop <= 15){
      return '';
    }
    if ($pop>15 && $pop<=30){
      return '#9fb8cf';
    }
    if ($pop>30 && $pop<=45){
      return '#7aa5cc';
    }
    if ($pop>45 && $pop<=60){
      return '#5c99d1';
    }
    if ($pop>60 && $pop<=75){
      return '#3596f0';
    }
    if ($pop>75 && $pop<=85){
      return '#1687f0';
    }
    if ($pop>85){
      return '#0082fa';
    }
  }

  function getOzono($oz){
    $arr = [];
    if ($oz <= 80){
      $arr['msg'] = 'Molto basso';
      $arr['color'] = '#1df2dd';
    }
    if ($oz>80 && $oz<120){
      $arr['msg'] = 'Basso';
      $arr['color'] = '#42fc77';
    }
    if ($oz>=120 && $oz<180){
      $arr['msg'] = 'Medio';
      $arr['color'] = '#cccf1b';
    }
    if ($oz>=180 && $oz<240){
      $arr['msg'] = 'Alto';
      $arr['color'] = '#f58f0a';
    }
    if ($oz>=240){
      $arr['msg'] = 'Molto alto';
      $arr['color'] = '#ff1e00';
    }
    return $arr;
  }
?>