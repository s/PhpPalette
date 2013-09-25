PhpPalette
==========

A PHP Application that finds out most common colors of an image.

First, application clones the image to the project folder, then process the data.

Second, new html file that uses <code>app.html</code> in the <code>outputs/templates</code> folder will be generated.

##Installation and Run

```
$ git clone git://github.com/s/PhpPalette.git ~/PhpPalette
$ cd ~/PhpPalette
$ php app.php ~/path/to/your/photo
```

##Skeletal
	<h3>Classes: (Core php files)</h3><br/>
	<code>Palette.php</code><br/>
	<code>Exception/</code><br/>

	<h3>Outputs:</h3><br/>
	<code>charts</code>         : Contains generated views. Generated views will be in the this folder.<br/>
	<code>data</code>        	: Contains moved image that will be processed.<br/>
	<code>logs</code>           : Contains log files that contains error logs.<br/>
	<code>templates</code>      : Contains template files that sets up the skeletal.<br/>

	<h3>Assets:</h3><br/>
	<code>css</code>         	: Contains css files.<br/>
	<code>font</code>        	: Contains font awesome font files.<br/>
	<code>img</code>           	: Contains images.<br/>
	<code>js</code>      		: Contains javascript codes in order to show popovers.<br/>

##Screenshot
![View Screen Shot](https://github.com/s/PhpPalette/blob/master/assets/img/ScreenShot.png?raw=true)