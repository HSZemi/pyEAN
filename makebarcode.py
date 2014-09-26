#! /usr/bin/env python3

import sys

svgheadtemplate = '''<?xml version="1.0" encoding="UTF-8" standalone="no"?>

<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   width="{0}"
   height="{1}"
   id="svg2"
   version="1.1">
   '''
   
svgtailtemplate = '''</svg>'''

svgrecttemplate = '''<rect
       style="color:#000000;fill:{0};fill-opacity:1;stroke:none;stroke-width:0.1;marker:none;visibility:visible;display:inline;overflow:visible;enable-background:accumulate"
       id="rect{1}"
       width="{4}"
       height="{5}"
       x="{2}"
       y="{3}" />
'''

svglettertemplate = '''<text
       xml:space="preserve"
       style="font-size:7px;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;line-height:125%;letter-spacing:0px;word-spacing:0px;fill:#000000;fill-opacity:1;stroke:none;font-family:Arial;-inkscape-font-specification:Arial"
       x="{1}"
       y="{2}"
       id="text{3}">{0}</text>
'''

def check_ean(inputstring):
	if(len(inputstring) != 13):
		return False
	val = []
	for c in inputstring:
		try:
			val.append(int(c))
		except ValueError:
			return False
	uneven = val[0]+val[2]+val[4]+val[6]+val[8]+val[10]+val[12]
	even = val[1]+val[3]+val[5]+val[7]+val[9]+val[11]
	if((uneven + even*3)%10 == 0):
		return val
	else:
		return False


if(len(sys.argv) != 2):
	print("Usage: {0} [EAN]".format(sys.argv[0]))
	sys.exit(1)
ean = check_ean(sys.argv[1])
if(len(sys.argv[1]) != 13 or not ean):
	print("{0} is not a supported EAN".format(sys.argv[1]))
	print("Usage: {0} [EAN]".format(sys.argv[0]))
	sys.exit(1)

codings13 = list(range(10))
codings13[0] = "UUUUUURRRRRR"
codings13[1] = "UUGUGGRRRRRR"
codings13[2] = "UUGGUGRRRRRR"
codings13[3] = "UUGGGURRRRRR"
codings13[4] = "UGUUGGRRRRRR"
codings13[5] = "UGGUUGRRRRRR"
codings13[6] = "UGGGUURRRRRR"
codings13[7] = "UGUGUGRRRRRR"
codings13[8] = "UGUGGURRRRRR"
codings13[9] = "UGGUGURRRRRR"

codings = list(range(10))
codings[0] = ("0001101","0100111","1110010")
codings[1] = ("0011001","0110011","1100110")
codings[2] = ("0010011","0011011","1101100")
codings[3] = ("0111101","0100001","1000010")
codings[4] = ("0100011","0011101","1011100")
codings[5] = ("0110001","0111001","1001110")
codings[6] = ("0101111","0000101","1010000")
codings[7] = ("0111011","0010001","1000100")
codings[8] = ("0110111","0001001","1001000")
codings[9] = ("0001011","0010111","1110100")

code = ""

for i in range(len(codings13[ean[0]])):
	c = codings13[ean[0]][i]
	idx = 0
	if(c == "U"):
		idx = 0
	elif(c == "G"):
		idx = 1
	elif(c == "R"):
		idx = 2
	code += codings[ean[i+1]][idx]

code2 = "101 " + code[:42] + " 01010 " + code[42:] + " 101"
code = "101" + code[:42] + "01010" + code[42:] + "101"
print(code2)

f = open("barcode.svg", "w")
print(svgheadtemplate.format(102, 34), file=f)

# draw bars
x = 6
y = 1
i = 0
for c in code[0:3]:
	if(c=="1"):
		color = "#000000"
		print(svgrecttemplate.format(color, i, x, y, 1, 30), file=f)
	i+=1
	x+=1
for k in range(3,45):
	c = code[k]
	if(c=="1"):
		if(code[k-1] == "0"):
			width = 1
			while(code[k+width] == "1"):
				width += 1
			color = "#000000"
			print(svgrecttemplate.format(color, i, x, y, width, 25), file=f)
	i+=1
	x+=1
for c in code[45:50]:
	if(c=="1"):
		color = "#000000"
		print(svgrecttemplate.format(color, i, x, y, 1, 30), file=f)
	i+=1
	x+=1
for k in range(50,92):
	c = code[k]
	if(c=="1"):
		if(code[k-1] == "0"):
			width = 1
			while(code[k+width] == "1"):
				width += 1
			color = "#000000"
			print(svgrecttemplate.format(color, i, x, y, width, 25), file=f)
	i+=1
	x+=1
for c in code[92:95]:
	if(c=="1"):
		color = "#000000"
		print(svgrecttemplate.format(color, i, x, y, 1, 30), file=f)
	i+=1
	x+=1

# write numbers
x = 1
y = 33
i = 0
print(svglettertemplate.format(ean[0], x, y, i), file=f)
i+=1
x = 11
for letter in ean[1:7]:
	print(svglettertemplate.format(letter, x, y, i), file=f)
	i+=1
	x+=7
x += 4
for letter in ean[7:13]:
	print(svglettertemplate.format(letter, x, y, i), file=f)
	i+=1
	x+=7
print(svgtailtemplate, file=f)
f.close()