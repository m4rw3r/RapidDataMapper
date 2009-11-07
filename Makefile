
# needed to make the phpdoc and tests to start properly
empty: ;


clean:	
	rm -fR report
	rm -fR dist
	rm -fR doc/api
	rm -f doc/manual.html
	rm -f dist.tar
	rm -f dist.tar.gz


# Create all documentation, not including API
doc: doc-html


# Generate documentation from Docbook
doc-html: empty
	xsltproc --xinclude --output doc/manual.html doc/docbook-xsl/xhtml/docbook.xsl doc/manual/src/book.xml


# Generate
phpdoc: empty
	phpdoc -t ./doc/api -d ./lib,./compat -j -o HTML:frames:DOM/earthli -s -ti "RapidDataMapper API Documentation"


# Test PHP compatibility
report-compatinfo:
	pci -d lib


# Run all unit tests and generate code coverage report
tests: empty
	PHPUnit --coverage-html report tests


# Package for distribution, run tests
dist: clean tests phpdoc doc-html
	mkdir dist
	git checkout-index -a -f --prefix=dist/
	cp doc/manual.html dist/doc/manual.html
	cp -R doc/api dist/doc/api
	cp -R report dist/doc/code_coverage_report
	tar -cf dist.tar dist/
	gzip dist.tar

