# dns

## Details
* points: 8
* category: net
* author: Anton
* flags: `cnctf{treat-domain-zones-as-public-knowledge}` or `.*ea7a56e3c09728491d60b37636313faa.*` (case insensitive)

## Public description:
The flag is stored in a TXT record on a subdomain of the domain `4a.si`. Flag format is `cnctf{.*}`. Subdomain is in format `cnctf-<32 random hex characters>.4a.si`.

## Deployment 
/

## Solution / writeup

### Solve method 1: AXFR zone transfer

The primary DNS server of `4a.si` can be obtained with `dig +short soa 4a.si` and it is at the moment `ns1.4a.si.`. You can direct a query directly to it with `dig @ns1.4a.si 4a.si aaaa` for example. It has zone transfers enabled for the entire world, so you can use `dig @ns1.4a.si 4a.si axfr` to get all records in the 4a.si domain. With the following command, you extract the flag from the "zone dump":

```
dig @ns1.4a.si 4a.si axfr | grep cnctf{
```

### Solve method 2: NSEC Zone walking

DNSSEC is a method of signing zones in the DNS. To simplify, each record gets a corresponding RRSIG record with the signature of the record. This RRSIG is passed along to any DNSSEC-capable resolvers so that they can cryptographically prove that no malicious resolver provided wrong responses.

But how would you sign the non-existance of a specific domain name? You can't just sign a message saying that "subdomain xxx does not exist", since DNSSEC is based on the requirement that the entire zone must be able to be signed completely offline and that resolvers for the domain may operate entirely without possession of the private signing key, only utilising presigned RRSIG records.

A solution to the problem of proving non-existance is to sign a record (called NSEC) that says that there are no subdomains between two subdomains (sorted alphabetically). Such NSEC records can be presigned (since we know all subdomains at sign-time) and stored on resolvers that don't do any cryptography. But the NSEC specification allows an attacker to obtain the entire zone with a process called NSEC zone-walking.

While we could walk and download the entire zone, since we know how the record starts (cnctf-), we can just query a non-existing `cnctf.4a.si` with DNSSEC requested and we get the searched-for secret subdomain in the NSEC record response:

```
dig +dnssec cnctf.4a.si | grep cnctf-
```

And then, to get the flag:

```
dig cnctf-ea7a56e3c09728491d60b37636313faa.4a.si txt
```
