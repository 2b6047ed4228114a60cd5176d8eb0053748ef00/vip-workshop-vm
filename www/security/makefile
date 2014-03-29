all: able permissions

able:
	wp --info &> /dev/null
	git --version &> /dev/null
	phantomjs --version &> /dev/null
	php -r 'if ( ! function_exists( "imagettfbbox" ) ) exit( 1 );'
	php -r 'if ( ! function_exists( "imagecreatetruecolor" ) ) exit( 1 );'
	@echo
	@echo '!!!SET WP_HOME, WP_SITEURL, and fix @@@ references!!!'
	@echo

permissions:
	touch ./attacks/7-clickjacking.png
	chmod a+rw ./attacks/7-clickjacking.png

db:
	-@read -p 'Create `database_name_here` Database and Import `evil_*` Data? [y/N]: '; case "$$REPLY" in [yY]*) make unsafeimport ;; *) echo Aborted; exit 1 ;; esac

unsafeimport: .db/db.sql
	-wp db create
	wp db import .db/db.sql
	wp db import .db/posts.sql

export:
	mysqldump --no-defaults --user=root --result-file=.db/db.sql database_name_here --tables evil_commentmeta evil_comments evil_links evil_options evil_postmeta evil_term_relationships evil_term_taxonomy evil_terms evil_usermeta evil_users evil_recent_colors
	mysqldump --no-defaults --user=root --result-file=.db/posts.sql database_name_here --tables evil_posts --where 'post_type<>"revision"'
