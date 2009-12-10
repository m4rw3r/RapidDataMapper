
# needed to make the phpdoc and tests to start properly
empty: ;


clean:	
	rm -fR report
	rm -fR dist
	rm -fR doc/api
	rm -fR doc/chunked
	rm -f doc/manual.html
	rm -f rdm.tar
	rm -f rdm.tar.gz
	rm -f rdm.zip
	rm -f manual.zip
	rm -f chunked.zip


# Create all documentation, not including API
doc: doc-html doc-chunk


# Generate documentation from Docbook
doc-html: empty
	xsltproc --xinclude --stringparam html.stylesheet manual.css --output doc/manual.html doc/docbook-xsl/xhtml/docbook.xsl doc/manual/src/book.xml


# Generate multiple html files
doc-chunk:
	mkdir -p doc/chunked
	xsltproc --xinclude --stringparam html.stylesheet manual.css --stringparam base.dir doc/chunked/ doc/docbook-xsl/xhtml/chunk.xsl doc/manual/src/book.xml
	cp doc/manual.css doc/chunked/manual.css


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
dist: clean tests phpdoc doc
	mkdir dist
	git checkout-index -a -f --prefix=dist/
	cp doc/manual.html dist/doc/manual.html
	cp doc/manual.css dist/doc/manual.css
	cp -R doc/chunked dist/doc/chunked
	cp -R doc/api dist/doc/api
	cp -R report dist/doc/code_coverage_report
	tar -cf rdm.tar dist/
	gzip rdm.tar
	zip -r rdm dist/*
	zip manual doc/manual.html doc/manual.css
	zip -r chunked doc/chunked

