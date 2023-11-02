# oebb

## Details
* points: 8
* category: forensic
* author: Anton
* flags: `cnctf{aztec_code}` or `cnctf{aztec_code} a`

## Public description:

A coworker sent me his railway ticket. But the name in the upper right corner in the corner of the ticket is obscured. Can you help me recover my coworker's surname that is hidden under the colourful piece of paper?

## Deployment

* Players should get `oebb.jpg`.

## Solution / writeup

This entire rail ticket is encoded according to the UIC918.3 specification in the Aztec code in the bottom right corner of the image. Use a suitable decoder, like the Android app `nl.waarisdetrein.myfirstrailpocket`, to decode the surname from the ticket.

