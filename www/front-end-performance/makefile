all: able

able:
	wp --info &> /dev/null
	git --version &> /dev/null
	@echo
	@echo '!!!SET WP_HOME WP_SITEURL and fix @@@ references!!!'
	@echo

db:
	-@read -p 'Create `frontend` Database and Import `fep_*` Data? [y/N]: '; case "$$REPLY" in [yY]*) make unsafeimport ;; *) echo Aborted; exit 1 ;; esac

unsafeimport: .db/db.sql
	-wp db create
	wp db import .db/db.sql

export:
	mysqldump --no-defaults --user=root --password=root --result-file=.db/db.sql frontend --tables fep_commentmeta fep_comments fep_links fep_options fep_postmeta fep_posts fep_term_relationships fep_term_taxonomy fep_terms fep_usermeta fep_users
