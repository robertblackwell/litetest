#makefile
BINDIR := ~/bin
BUILDDIR:=	build
BINNAME := litetest	
PHARNAME := litetest.phar
TARGET := 	build/$(PHARNAME)

PHARIZER := scripts/create-phar.php
SRCPREFIX := `pwd`

php :=  $(shell find classes -type f -name "*.php") $(shell find vendor/kevinlebrun -type f -name "*.php")  

build/litetest: $(TARGET)
	# take the phar extension off the end

$(TARGET): Makefile $(PHARIZER) $(php) bump
	# create the phar
	$(PHARIZER) $(TARGET) $(SRCPREFIX) classes/Stub.php classes vendor/kevinlebrun 
	#make it executable
	chmod 775 $(TARGET)

install:
	cp -v $(TARGET) $(BINDIR)/$(BINNAME)

clean:
	rm -v $(BUILDDIR)/*

bump:
	./build/bump.php

dump:
	echo $(php)

