countries <- read.table("Countries.txt", sep="\t", header=T, quote="")

mysql_table <- "$wpdb->projectmanager_countries"

sink("CountriesSQL.php", split=T)
cat("<?php\n");
for (r in 1:nrow(countries)) {
	if (countries[r,]$Code != "")
		cat(paste("$wpdb->query( $wpdb->prepare(\"INSERT INTO {", mysql_table, "} (code, name) VALUES ('", countries[r,]$Code, "', '%s')\", \"",countries[r,]$Name,"\" ));\n", sep=""))
}
cat("?>");
sink()


mysql_table <- "wp_projectmanager_countries"
sink("Countries.sql", split=T)
for (r in 1:nrow(countries)) {
	if (countries[r,]$Code != "")
		cat(paste("INSERT INTO {", mysql_table, "} (code, name) VALUES ('", countries[r,]$Code, "', '", countries[r,]$Name, "');\n", sep=""))
}
sink()


sink("Countries.pot", split=T)
for (r in 1:nrow(countries)) {
	cat("#: Countries Database\n")
	cat(paste("msgid \"", countries[r,]$Name, "\"\n", sep=""))
	cat("msgstr \"\"\n\n")
}
sink()