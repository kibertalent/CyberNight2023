default: 1bitvm.tar.gz run

server.out: 1bitvm server.asm
	1bitvm/asm.py -d server.asm 2>&1 > asm.log

server.asm: server.php index.html hi.html
	./server.php > $@

run: server.out
	socat TCP6-LISTEN:6844,fork,reuseaddr 'exec:1bitvm/main.py server.out'

1bitvm:
	git clone https://ass.si/git/adrian/1bitvm

clean:
	rm -fr `cat .gitignore`

1bitvm.tar.gz: server.out docker-compose.yml Dockerfile
	f=`grep -o cnctf{.*} docker-compose.yml` && sed -i s/$$f/cnctf{placeholder}/ docker-compose.yml && cd .. && tar --exclude *README.md --exclude *challenge.yml --exclude *1bitvm.tar.gz -zc `rev <<<$$OLDPWD | cut -d/ -f1 | rev` | sponge $$OLDPWD/1bitvm.tar.gz; cd $$OLDPWD; sed -i s/cnctf{placeholder}/$$f/ docker-compose.yml

.PHONY: default clean run
