<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\Request;

class Pagination
{
  /**
   * Decoupe les tableaux venant de la requete
   */
  public function chunkTable(Request $request, $table, $limit)
  {
    $chunks = array_chunk($table, $limit);

    if (count($table) > 0) {
      $current_chunk = $chunks[0];
    } else {
      $current_chunk = [];
    }

    $page = intval($request->query->get('page'));

    if ($page > 0 && $page <= count($chunks)) {
      if (isset($chunks[$page - 1])) {
        $current_chunk = $chunks[$page - 1];
      }
    }

    return [
      'current_chunk' => $current_chunk,
      'chunks' => $chunks,
    ];
  }

  /**
   * Genère les liens pour chaque bout de tableau
   */
  public function generatePaginationLinks($total_pages, $current_page, $filter = null, $statut = null, $limit = null)
  {
    $links = [];
    for ($i = 1; $i <= $total_pages; $i++) {
      $url = '?page=' . $i;

      if ($limit) {
        $url .= '&limit=' . urldecode($limit);
      }

      if ($filter) {
        $url .= '&filter=' . urlencode($filter);

        if ($limit) {
          $url .= '&limit=' . urldecode($limit);
        }

        if ($statut) {
          $url .= '&statut=' . urlencode($statut);

          if ($limit) {
            $url .= '&limit=' . urldecode($limit);
          }
        }
      }

      $links[] = [
        'label' => $i,
        'url' => $url,
        'active' => $i == $current_page || (!$current_page && $i == 1),
      ];
    }
    return $links;
  }
}
