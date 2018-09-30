<?php

add_action('rest_api_init', 'univeristyRegisterSearch');

function univeristyRegisterSearch()
{
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'universitySearchResults',
    ));
}

function universitySearchResults($data)
{
    $mainQuery = new WP_QUERY(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'event', 'campus'),
        's' => sanitize_text_field($data['term']),
    ));

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array(),
    );

    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();

        if (get_post_type() == 'post' or get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => 'post',
                'authorName' => get_the_author(),
            ));
        }

        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'pofessorLandscape')
            ));
        }

        if (get_post_type() == 'program') {
          $relatedCampuses = get_field('related_campus');

          if($relatedCampuses){
            foreach($relatedCampuses as $campus){
              array_push($results['campuses'], array(
                'title' => get_the_title($campus),
                'permalink' => get_the_permalink($campus)
              ));
            }
          }
         

            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
            ));
        }

        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => wp_trim_words(get_the_content(), 15)
            ));
        }

        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        }

    }

    //this code is to add the relationship to the search results

    if ($results['programs']) {
        $programsMetaQuery = array('relation' => 'OR');

        foreach ($results['programs'] as $item) {
            array_push($programsMetaQuery, array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"',
            ));
        }

        $programRelationShipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery,
        ));

        while ($programRelationShipQuery->have_posts()) {
            $programRelationShipQuery->the_post();

            if (get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => wp_trim_words(get_the_content(), 15),
                ));
            }

            if (get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'pofessorLandscape'),
                ));
            }

        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));

    }

    return $results;
}
