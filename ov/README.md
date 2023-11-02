# 1bitvm

## Details
* points: 8
* category: misc
* author: Adrian & Anton
* flags:  `cnctf{hard2pwnUnknownArch}` or `cnctf{hard2pwnUnknownArch}---` (same flag)


## Description:
1bitvm is a cursed virtual machine for a cursed imaginary architecture. A program is written that combines the horrors of PHP, python and architecture specific assembly language into a giant mess that somehow servers HTTP requests. It's your job to convince the "webserver" to read you the flag from it's RAM, totaling not more than 128 bits.

## Deployment 
* Deploy the docker image with `docker compose up`. It is listening on port 6844, map it to whatever port is most convenient.
	- alternatively you can just run `make run`
* Players should get 1bitvm.tar.gz created with `make 1bitvm.tar.gz`. It doesn't contain solutions and flags.

## Solution / writeup
The `win` subroutine reads the flag from the virtual ROM. Since content is delivered based on the first two bytes of the path (control "jumps" to first two bytes of request path casted to address ORed with 0x8000), players can also jump to the `win` subroutine. The address of `win:` label can be read from `asm.log` file (`win na 0xe339`). Since the first bit of the address will always be set to 1, 0x63 (ASCII 'c') and 0x39 (ASCII '9') can be set as request path to jump to the `win` subroutine and print the flag.
