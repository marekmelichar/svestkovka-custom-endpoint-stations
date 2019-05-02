<?php

// get request for fetch train stops and their IDs:
// https://svestkovka.marekmelichar.cz/wp-json/svestkovka/v1/stations?date=2020-05-05

add_action('rest_api_init', function () {
  register_rest_route('svestkovka/v1', '/stations', array(
    'methods' => 'GET',
    'callback' => 'get_stations_func',
    'args' => array(
      
    ),
    'permission_callback' => function () {
      // return current_user_can( 'edit_others_posts' );
      return true;
    }
  ));
});

function get_stations_func($data)
{
  $search_date = $data->get_param('date');

  // echo $search_date;

  global $wpdb;

  if($search_date) {
    $querySpoje = "CALL dejPlatneStaniceProDatum('$search_date');";
  } else {
    $querySpoje = "CALL dejPlatneStaniceProDatum(null);";
  }

  $result_stanice = $wpdb->get_results($querySpoje);

  $response = array();

  foreach ($result_stanice as $item) {
    $response[] = array(
      'idStanice' => $item->idStanice,
      'nazevStanice' => $item->nazevStanice
    );
  }

  echo json_encode($response);

	wp_die(); // this is required to terminate immediately and return a proper response
}