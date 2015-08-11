<?php
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AFG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AFG', '%s')", "Afghanistan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ALA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ALA', '%s')", "Åland Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ALB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ALB', '%s')", "Albania" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'DZA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('DZA', '%s')", "Algeria" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ASM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ASM', '%s')", "American Samoa" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AND'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AND', '%s')", "Andorra" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AGO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AGO', '%s')", "Angola" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AIA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AIA', '%s')", "Anguilla" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ATG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ATG', '%s')", "Antigua and Barbuda" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ARG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ARG', '%s')", "Argentina" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ARM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ARM', '%s')", "Armenia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ABW'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ABW', '%s')", "Aruba" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AUS'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AUS', '%s')", "Australia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AUT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AUT', '%s')", "Austria" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'AZE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('AZE', '%s')", "Azerbaijan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BHS'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BHS', '%s')", "Bahamas" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BHR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BHR', '%s')", "Bahrain" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BGD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BGD', '%s')", "Bangladesh" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BRB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BRB', '%s')", "Barbados" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BLR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BLR', '%s')", "Belarus" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BEL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BEL', '%s')", "Belgium" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BLZ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BLZ', '%s')", "Belize" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BEN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BEN', '%s')", "Benin" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BMU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BMU', '%s')", "Bermuda" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BTN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BTN', '%s')", "Bhutan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BOL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BOL', '%s')", "Bolivia (Plurinational State of)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BES'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BES', '%s')", "Bonaire, Sint Eustatius and Saba" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BIH'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BIH', '%s')", "Bosnia and Herzegovina" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BWA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BWA', '%s')", "Botswana" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BRA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BRA', '%s')", "Brazil" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VGB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VGB', '%s')", "British Virgin Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BRN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BRN', '%s')", "Brunei Darussalam" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BGR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BGR', '%s')", "Bulgaria" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BFA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BFA', '%s')", "Burkina Faso" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BDI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BDI', '%s')", "Burundi" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CPV'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CPV', '%s')", "Cabo Verde" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KHM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KHM', '%s')", "Cambodia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CMR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CMR', '%s')", "Cameroon" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CAN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CAN', '%s')", "Canada" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CYM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CYM', '%s')", "Cayman Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CAF'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CAF', '%s')", "Central African Republic" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TCD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TCD', '%s')", "Chad" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CHL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CHL', '%s')", "Chile" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CHN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CHN', '%s')", "China" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'HKG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('HKG', '%s')", "China, Hong Kong Special Administrative Region" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MAC'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MAC', '%s')", "China, Macao Special Administrative Region" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'COL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('COL', '%s')", "Colombia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'COM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('COM', '%s')", "Comoros" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'COG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('COG', '%s')", "Congo" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'COK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('COK', '%s')", "Cook Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CRI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CRI', '%s')", "Costa Rica" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CIV'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CIV', '%s')", "Côte d'Ivoire" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'HRV'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('HRV', '%s')", "Croatia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CUB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CUB', '%s')", "Cuba" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CUW'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CUW', '%s')", "Curaçao" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CYP'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CYP', '%s')", "Cyprus" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CZE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CZE', '%s')", "Czech Republic" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PRK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PRK', '%s')", "Democratic People's Republic of Korea" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'COD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('COD', '%s')", "Democratic Republic of the Congo" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'DNK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('DNK', '%s')", "Denmark" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'DJI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('DJI', '%s')", "Djibouti" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'DMA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('DMA', '%s')", "Dominica" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'DOM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('DOM', '%s')", "Dominican Republic" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ECU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ECU', '%s')", "Ecuador" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'EGY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('EGY', '%s')", "Egypt" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SLV'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SLV', '%s')", "El Salvador" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GNQ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GNQ', '%s')", "Equatorial Guinea" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ERI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ERI', '%s')", "Eritrea" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'EST'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('EST', '%s')", "Estonia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ETH'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ETH', '%s')", "Ethiopia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'FRO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('FRO', '%s')", "Faeroe Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'FLK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('FLK', '%s')", "Falkland Islands (Malvinas)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'FJI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('FJI', '%s')", "Fiji" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'FIN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('FIN', '%s')", "Finland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'FRA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('FRA', '%s')", "France" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GUF'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GUF', '%s')", "French Guiana" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PYF'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PYF', '%s')", "French Polynesia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GAB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GAB', '%s')", "Gabon" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GMB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GMB', '%s')", "Gambia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GEO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GEO', '%s')", "Georgia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'DEU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('DEU', '%s')", "Germany" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GHA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GHA', '%s')", "Ghana" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GIB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GIB', '%s')", "Gibraltar" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GRC'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GRC', '%s')", "Greece" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GRL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GRL', '%s')", "Greenland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GRD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GRD', '%s')", "Grenada" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GLP'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GLP', '%s')", "Guadeloupe" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GUM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GUM', '%s')", "Guam" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GTM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GTM', '%s')", "Guatemala" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GGY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GGY', '%s')", "Guernsey" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GIN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GIN', '%s')", "Guinea" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GNB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GNB', '%s')", "Guinea-Bissau" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GUY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GUY', '%s')", "Guyana" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'HTI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('HTI', '%s')", "Haiti" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VAT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VAT', '%s')", "Holy See" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'HND'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('HND', '%s')", "Honduras" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'HUN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('HUN', '%s')", "Hungary" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ISL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ISL', '%s')", "Iceland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'IND'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('IND', '%s')", "India" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'IDN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('IDN', '%s')", "Indonesia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'IRN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('IRN', '%s')", "Iran (Islamic Republic of)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'IRQ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('IRQ', '%s')", "Iraq" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'IRL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('IRL', '%s')", "Ireland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'IMN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('IMN', '%s')", "Isle of Man" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ISR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ISR', '%s')", "Israel" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ITA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ITA', '%s')", "Italy" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'JAM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('JAM', '%s')", "Jamaica" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'JPN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('JPN', '%s')", "Japan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'JEY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('JEY', '%s')", "Jersey" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'JOR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('JOR', '%s')", "Jordan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KAZ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KAZ', '%s')", "Kazakhstan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KEN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KEN', '%s')", "Kenya" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KIR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KIR', '%s')", "Kiribati" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KWT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KWT', '%s')", "Kuwait" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KGZ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KGZ', '%s')", "Kyrgyzstan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LAO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LAO', '%s')", "Lao People's Democratic Republic" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LVA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LVA', '%s')", "Latvia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LBN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LBN', '%s')", "Lebanon" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LSO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LSO', '%s')", "Lesotho" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LBR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LBR', '%s')", "Liberia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LBY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LBY', '%s')", "Libya" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LIE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LIE', '%s')", "Liechtenstein" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LTU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LTU', '%s')", "Lithuania" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LUX'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LUX', '%s')", "Luxembourg" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MDG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MDG', '%s')", "Madagascar" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MWI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MWI', '%s')", "Malawi" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MYS'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MYS', '%s')", "Malaysia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MDV'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MDV', '%s')", "Maldives" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MLI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MLI', '%s')", "Mali" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MLT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MLT', '%s')", "Malta" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MHL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MHL', '%s')", "Marshall Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MTQ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MTQ', '%s')", "Martinique" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MRT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MRT', '%s')", "Mauritania" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MUS'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MUS', '%s')", "Mauritius" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MYT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MYT', '%s')", "Mayotte" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MEX'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MEX', '%s')", "Mexico" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'FSM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('FSM', '%s')", "Micronesia (Federated States of)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MCO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MCO', '%s')", "Monaco" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MNG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MNG', '%s')", "Mongolia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MNE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MNE', '%s')", "Montenegro" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MSR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MSR', '%s')", "Montserrat" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MAR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MAR', '%s')", "Morocco" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MOZ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MOZ', '%s')", "Mozambique" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MMR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MMR', '%s')", "Myanmar" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NAM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NAM', '%s')", "Namibia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NRU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NRU', '%s')", "Nauru" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NPL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NPL', '%s')", "Nepal" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NLD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NLD', '%s')", "Netherlands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NCL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NCL', '%s')", "New Caledonia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NZL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NZL', '%s')", "New Zealand" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NIC'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NIC', '%s')", "Nicaragua" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NER'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NER', '%s')", "Niger" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NGA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NGA', '%s')", "Nigeria" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NIU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NIU', '%s')", "Niue" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NFK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NFK', '%s')", "Norfolk Island" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MNP'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MNP', '%s')", "Northern Mariana Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'NOR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('NOR', '%s')", "Norway" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'OMN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('OMN', '%s')", "Oman" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PAK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PAK', '%s')", "Pakistan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PLW'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PLW', '%s')", "Palau" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PAN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PAN', '%s')", "Panama" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PNG'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PNG', '%s')", "Papua New Guinea" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PRY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PRY', '%s')", "Paraguay" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PER'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PER', '%s')", "Peru" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PHL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PHL', '%s')", "Philippines" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PCN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PCN', '%s')", "Pitcairn" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'POL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('POL', '%s')", "Poland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PRT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PRT', '%s')", "Portugal" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PRI'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PRI', '%s')", "Puerto Rico" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'QAT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('QAT', '%s')", "Qatar" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KOR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KOR', '%s')", "Republic of Korea" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MDA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MDA', '%s')", "Republic of Moldova" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'REU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('REU', '%s')", "Réunion" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ROU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ROU', '%s')", "Romania" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'RUS'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('RUS', '%s')", "Russian Federation" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'RWA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('RWA', '%s')", "Rwanda" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'BLM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('BLM', '%s')", "Saint Barthélemy" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SHN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SHN', '%s')", "Saint Helena" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'KNA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('KNA', '%s')", "Saint Kitts and Nevis" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LCA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LCA', '%s')", "Saint Lucia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MAF'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MAF', '%s')", "Saint Martin (French part)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SPM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SPM', '%s')", "Saint Pierre and Miquelon" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VCT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VCT', '%s')", "Saint Vincent and the Grenadines" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'WSM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('WSM', '%s')", "Samoa" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SMR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SMR', '%s')", "San Marino" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'STP'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('STP', '%s')", "Sao Tome and Principe" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SAU'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SAU', '%s')", "Saudi Arabia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SEN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SEN', '%s')", "Senegal" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SRB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SRB', '%s')", "Serbia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SYC'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SYC', '%s')", "Seychelles" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SLE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SLE', '%s')", "Sierra Leone" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SGP'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SGP', '%s')", "Singapore" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SXM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SXM', '%s')", "Sint Maarten (Dutch part)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SVK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SVK', '%s')", "Slovakia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SVN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SVN', '%s')", "Slovenia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SLB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SLB', '%s')", "Solomon Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SOM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SOM', '%s')", "Somalia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ZAF'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ZAF', '%s')", "South Africa" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SSD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SSD', '%s')", "South Sudan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ESP'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ESP', '%s')", "Spain" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'LKA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('LKA', '%s')", "Sri Lanka" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'PSE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('PSE', '%s')", "State of Palestine" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SDN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SDN', '%s')", "Sudan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SUR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SUR', '%s')", "Suriname" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SJM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SJM', '%s')", "Svalbard and Jan Mayen Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SWZ'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SWZ', '%s')", "Swaziland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SWE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SWE', '%s')", "Sweden" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'CHE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('CHE', '%s')", "Switzerland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'SYR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('SYR', '%s')", "Syrian Arab Republic" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TJK'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TJK', '%s')", "Tajikistan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'THA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('THA', '%s')", "Thailand" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'MKD'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('MKD', '%s')", "The former Yugoslav Republic of Macedonia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TLS'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TLS', '%s')", "Timor-Leste" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TGO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TGO', '%s')", "Togo" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TKL'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TKL', '%s')", "Tokelau" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TON'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TON', '%s')", "Tonga" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TTO'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TTO', '%s')", "Trinidad and Tobago" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TUN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TUN', '%s')", "Tunisia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TUR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TUR', '%s')", "Turkey" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TKM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TKM', '%s')", "Turkmenistan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TCA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TCA', '%s')", "Turks and Caicos Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TUV'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TUV', '%s')", "Tuvalu" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'UGA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('UGA', '%s')", "Uganda" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'UKR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('UKR', '%s')", "Ukraine" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ARE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ARE', '%s')", "United Arab Emirates" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'GBR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('GBR', '%s')", "United Kingdom of Great Britain and Northern Ireland" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'TZA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('TZA', '%s')", "United Republic of Tanzania" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'USA'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('USA', '%s')", "United States of America" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VIR'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VIR', '%s')", "United States Virgin Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'URY'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('URY', '%s')", "Uruguay" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'UZB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('UZB', '%s')", "Uzbekistan" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VUT'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VUT', '%s')", "Vanuatu" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VEN'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VEN', '%s')", "Venezuela (Bolivarian Republic of)" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'VNM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('VNM', '%s')", "Viet Nam" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'WLF'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('WLF', '%s')", "Wallis and Futuna Islands" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ESH'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ESH', '%s')", "Western Sahara" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'YEM'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('YEM', '%s')", "Yemen" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ZMB'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ZMB', '%s')", "Zambia" ));
}
if ($wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->projectmanager_countries} WHERE code = 'ZWE'") == 0) {
    $wpdb->query( $wpdb->prepare("INSERT INTO {$wpdb->projectmanager_countries} (code, name) VALUES ('ZWE', '%s')", "Zimbabwe" ));
}
?>