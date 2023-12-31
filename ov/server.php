#!/usr/bin/env php
;; This VM has 128 bits of RAM so creating loops is hard. Instad this "HTTP" server works by unrolling all loops in assembly. To achieve that, assembly is generated by preprocessing with PHP. You can see the output file after running make in server.asm.
<?php
function escape ($input) { // escapes a byte string $input by converting every byte into \xHH.
        return implode("", array_map(function ($element) { return "\\x" . bin2hex($element); }, str_split($input)));
}
?>

;; start writing the following code to address 4 in ROM
.org 4

;; include standard library
%include "1bitvm/std.asm"

;; assembly macro that prints two bytes (black box)
%macro print2 2
    c16 %1, %2, 1
    set_out_b %2 + 0 , 0x14
    set_out_b %2 + 1 , 0x14
    set_out_b %2 + 2 , 0x14
    set_out_b %2 + 3 , 0x14
    set_out_b %2 + 4 , 0x14
    set_out_b %2 + 5 , 0x14
    set_out_b %2 + 6 , 0x14
    set_out_b %2 + 7 , 0x14
    set_out_b %2 + 8 , 0x14
    set_out_b %2 + 9 , 0x14
    set_out_b %2 + 10, 0x14
    set_out_b %2 + 11, 0x14
    set_out_b %2 + 12, 0x14
    set_out_b %2 + 13, 0x14
    set_out_b %2 + 14, 0x14
    set_out_b %2 + 15, 0x14
%endm

;; header that is sent in every response, 38 bytes
header:
<?php
$header = "HTTP/1.0 200\nContent-Type: text/html\n\n";
?>
.db b"<?= escape($header) ?>"

;; label that points to defined bytes as a string literal in ROM
flag:
.db b"<?= getenv("flag") ?: "cnctf{placeholder}" ?>"

;; storage for hi page
hi:
.db b"<?= escape(file_get_contents("hi.html")) ?>"

;; label that points to defined bytes as a string literal in ROM
;; PHP is only used as a preprocessor for generating assembly with this bytestring read from a file
index:
.db b"<?= escape(file_get_contents("index.html")) ?>"

print_header_pointer:
.orgr 2

;; prints header and jumps to address stored in 0x30 + 1
print_header:
<?php
	for ($i = 0; $i < strlen($header)/2; $i++)
		echo "	print2 labels['header']+$i, 0x15" . PHP_EOL;
?>
	ret

.org labels["print_header_pointer"]*2
.db by2(labels["print_header"])

;; the string b' H' will be read as two byte request-path (GET /< H>TTP/1.0) when requesting with empty path
.org (int.from_bytes(b' H') | 0x8000)*2
;; PHP generated assembly subroutine that prints bytes at label word. creating a loop on this VM would be hard.
print_index:
	call labels["print_header_pointer"], 1
<?php
	for ($i = 0; $i < strlen(file_get_contents("index.html"))/2; $i++)
		echo "	print2 labels['index']+$i, 0x15" . PHP_EOL;
?>
	exit

;; hi page, the & 0xfffe means last bit will always be 0
.org (int.from_bytes(b'YO') | 0x8000)*2
print_hi:
	call labels["print_header_pointer"], 1
<?php
	for ($i = 0; $i < strlen(file_get_contents("hi.html"))/2; $i++)
		echo "	print2 labels['hi']+$i, 0x15" . PHP_EOL;
?>
	exit

;; sends the flag to the client
win:
	call labels["print_header_pointer"], 1
<?php
	for ($i = 0; $i < strlen(getenv("flag") ?: "cnctf{placeholder}")/2; $i++)
		echo "	print2 labels['flag']+$i, 0x15" . PHP_EOL;
?>
	exit

main:
	;; we set first bit of address to 1 -- all pages are on addresses 0x8000 and above, first two letters of request path are casted to an address and ORed with 0x8000
<?php
	for ($i = 0; $i < strlen("GET /")*8; $i++) // read the first 5 bytes and discard them, they will always be the same (GET /)
		echo "get_in_b 0x15, 0x14" . PHP_EOL . "set0 IN_A" . PHP_EOL;
	for ($i = 0; $i < 16; $i++) // read two bytes, it will be used for selecting a page to send to the client
		echo "get_in_b 0x30+$i, 0x14" . PHP_EOL . "set0 IN_A" . PHP_EOL;
?>
	set1 0x30
	;; jump to print subroutine
	c16 0x30, 0, 0

init "main"
