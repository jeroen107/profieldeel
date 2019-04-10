<?php
//voorbeeld loop

function views_voorbeeld(){
  $data = db_pdo_select_all("SELECT id, naam, foto FROM behandelingen");

  if (is_array($data) and count($data) > 0){
    foreach ($data as $key => $value) {
    	$url = pages_url('behandelingen', $value["id"]);
        $html .=
        '<div class="col-md-6 col-lg-4 card">
            <a class="card-inner" href="'.$url.'">
                <div class="card-content">
                    <img src="'.$value["foto"].'">
                    <p>'.$value["naam"].'</p>
                </div>
            </a>
        </div>';
    }
  }

    return $html;
}
?>
