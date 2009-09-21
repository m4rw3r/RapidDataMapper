
empty: ;

clean:	
	rm -fR report
	rm -fR dist
	rm -fR api
	rm -f doc/manual.html
	rm -f dist.tar
	rm -f dist.tar.gz

doc-html: empty
	xsltproc --xinclude --output doc/manual.html doc/docbook-xsl/xhtml/docbook.xsl doc/manual/src/book.xml

phpdoc: empty
	phpdoc -t ./api -d ./lib,./compat -j -o HTML:frames:DOM/earthli -s -ti "RapidDataMapper API Documentation"

report-compatinfo:
	pci -d lib

tests: empty
	PHPUnit --coverage-html report tests

dist: clean tests phpdoc doc-html
	mkdir dist
	git checkout-index -a -f --prefix=dist/
	cp doc/manual.html dist/manual.html
	cp -R api dist/api
	cp -R report dist/report
	tar -cf dist.tar dist/
	gzip dist.tar

