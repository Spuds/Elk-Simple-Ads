<?php
// Version: 1.0.1; ManageAds

// Ads
$txt['sa_ads_name'] = 'Nom';
$txt['sa_ads_body_template'] = 'Modèle de corps';
$txt['sa_ads_body'] = 'Corps';
$txt['sa_ads_status'] = 'Statut';

$txt['sa_ads_click_limit'] = 'Limite de clic';
$txt['sa_ads_impression_limit'] = 'Limite d\'impression';
$txt['sa_ads_expiration'] = 'Expiration';
$txt['sa_ads_created'] = 'Créée';

$txt['sa_ads_positions'] = 'Positions';
$txt['sa_ads_membergroups'] = 'Groupes de membres';
$txt['sa_ads_actions'] = 'Actions';
$txt['sa_ads_boards'] = 'Sections';
$txt['sa_ads_custom_display'] = 'Affichage personnalisé';

$txt['sa_ads_clicks'] = 'Clics';
$txt['sa_ads_impressions'] = 'Impressions';
$txt['sa_ads_expires'] = 'Expirations';

$txt['sa_no_ads'] = 'Aucun pub de crée.';

// Positions
$txt['sa_positions_name'] = 'Nom';
$txt['sa_positions_namespace'] = 'Emplacement';
$txt['sa_positions_type'] = 'Type';

$txt['sa_positions_notice_title'] = 'Position Créée';
$txt['sa_positions_notice_body'] = '<p>La position a été créée avec succès !</p><p>Afin d\'afficher les pub pour cette position, merci d\'ajouter l\'appel de fonction suivante à l\'endoit où vous voulez que la pub soit affichée :<br /><br /><code class="bbc_code">template_ad_position(\'%1$s\');</code></p>';
$txt['sa_no_positions'] = 'Aucune position de créée.';

// Generic
$txt['sa_generic_actions'] = 'Actions';
$txt['sa_generic_continue'] = 'Continuer';
$txt['sa_generic_edit'] = 'Editer';
$txt['sa_generic_delete'] = 'Supprimer';

$txt['sa_generic_active'] = 'Activer';
$txt['sa_generic_disabled'] = 'Désactiver';

$txt['sa_generic_expired'] = 'Expiré';
$txt['sa_generic_never'] = 'Jamais';

$txt['sa_generic_membergroup'] = 'Groupe de membres';
$txt['sa_generic_groups_allow'] = 'A';
$txt['sa_generic_groups_disallow'] = 'X';
$txt['sa_generic_groups_deny'] = 'D';

$txt['sa_generic_expire_days'] = 'Jour(s)';
$txt['sa_generic_expire_months'] = 'Moos(s)';
$txt['sa_generic_expire_years'] = 'An(s)';

$txt['sa_generic_board_index'] = 'Index des sections';

$txt['sa_generic_plain'] = 'Plein';
$txt['sa_generic_rotating'] = 'Rotation';

// Errors
$txt['sa_error_empty_name'] = 'Vous avez laissé le nom du champ vide.';
$txt['sa_error_empty_body'] = 'Vous avez laissé le corps du champs vide.';
$txt['sa_error_empty_namespace'] = 'Vous avez laissé\'emplacement vide.';
$txt['sa_error_duplicate_namespace'] = 'L\'emplacement sélectionné est déjé utilisé. Les emplacements sont uniques.';
$txt['sa_error_invalid_namespace'] = 'Les emplacement ne peuvent contenir que des lettres, chiffres et underscore.';

// Admin
$txt['sa_admin_cat_title'] = 'SimpleAds';
$txt['sa_admin_title'] = 'Publicités';
$txt['sa_admin_desc'] = 'Vous pouvez gérer vos publicités et vos emplacements dans cette section.';
$txt['sa_ads_list_title'] = 'Liste des pubs';
$txt['sa_ads_add_title'] = 'Ajouter une pub';
$txt['sa_ads_edit_title'] = 'Editer une pub';
$txt['sa_positions_list_title'] = 'Liste des positions';
$txt['sa_positions_add_title'] = 'Ajouter une position';
$txt['sa_positions_edit_title'] = 'Editer une position';

$helptxt['sa_ads_custom_display'] = 'Utiliser ce champ pour définir une(des) action(s) spécifique(s) sur la pub à afficher, par exemple {action} == \'contact\' || {action} == \'forum\'.  Vous pouvez aussi toutes les utiliser. A utiliser si l\'action n\'est pas une des actions standard définies au-dessus';