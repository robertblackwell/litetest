#makefile
BINDIR := ~/bin
BUILDDIR:=	build
PHARNAME := litetest.phar
TARGET := 	build/$(PHARNAME)
PHARIZER := scripts/create-phar.php
SRCPREFIX := `pwd`

php :=  $(shell find classes -type f -name "*.php") $(shell find vendor -type f -name "*.php")  

build/litetest: $(TARGET)
	# take the phar extension off the end

$(TARGET): Makefile $(PHARIZER) $(php)
	# create the phar
	$(PHARIZER) $(TARGET) $(SRCPREFIX) classes/Stub.php classes vendor 
	#make it executable
	chmod 775 $(TARGET)

install:
	cp -v $(TARGET) $(BINDIR)/$(PHARNAME)

clean:
	rm -v $(BUILDDIR)/*