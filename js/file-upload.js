(function($){
	$.fn.gaptekmediaUpload = function(options){
		
		var settings = $.extend({
			uploadUrl         : null,
			removeImgUrl        : null,
			fontStyle    : null,
			success: function(r){console.log(r)}
		}, options);
		
		var upload_dir;
					
		this.change(function(){
			upload(this.files[0], $(this));
		});
		
		var upload = function (file, t){
			enableSameFileUpload(t);
			showAnimatedLoader(t);
			showProgressBar(t);
			
			var formData = new FormData();
			formData.append("upload_file", file);
			formData.append("attach_id", t.attr('id'));
			formData.append("uid", uid);
						
			id = t.attr('id');
			if(window['u'+id] != null){
				window['u'+id].abort();
			}
			
			window['u'+id] = $.ajax({
				url: settings.uploadUrl,
				type: 'POST',
				xhr: function() {
					myXhr = $.ajaxSettings.xhr();
					if(myXhr.upload)
						myXhr.upload.addEventListener('progress',function(e){						
						if(e.lengthComputable){
							$("#progress").text(e.loaded + " / " + e.total);
							perc = Math.floor(e.loaded / e.total * 100);
							t.parent().prev().find('.progress-bar').width(perc+'%');
						}
					}, false); 
					
					return myXhr;
				},
				success: function(r)
				{
					previewImage(file, t);
					hideProgressBar(t);
					showRemoveBtn(t);
					console.log(r);
					settings.success(r);
					/* save image id to #img_id */
					$('#img_'+r.img_id).val(r.attach_id);
					upload_dir = r.image_folder;
					
				},
				data: formData,
				cache: false,
				contentType: false,
				processData: false
			});
		}		
		
		var previewImage = function(file, t) {
			var reader = new FileReader();
			reader.onload = (function(th) { 
				return function(e) { 
					th.parent('label').removeAttr("style").removeClass('loader').css({
						'background' : 'url('+(e.target.result)+') no-repeat center',
						'background-size' : 'auto 100%'
					});
				}; 
			})(t);
			reader.readAsDataURL(file);
		}
		
		var showAnimatedLoader = function(t){
			t.parent().removeAttr("style").css({
				'background-image': 'url(loader.gif )',
				'background-color':'gray'
			});
		}
		
		var showProgressBar = function(t){			
			/* nol kan dulu persentasenya	 */
			t.parent().prev().find('.progress-bar').width('0%');
			t.parent().prev().show();
		}
		
		var hideProgressBar = function(t){
			t.parent().prev().hide();
		}
		
		
		var showRemoveBtn = function(t){
			t.parent().prev().prev().show();
		}
		
		var enableSameFileUpload = function(t){
			t.val('');
		}		
		
		$('.btn-remove').click(function(e){
			e.preventDefault();
			
			image_id = $(this).prev().val();
			console.log(image_id);
			data = {
				image_id: image_id,
				dir: upload_dir
			};
			
			
			$.post(settings.removeImgUrl, data, function(r){
				console.log(r);
			});
			
			/* hide remove btn wrapper */
			$(this).parent().hide();
			
			/* replace image with add icon */
			$(this).parent().next().next().removeAttr("style").removeClass('loader').css({
				'background-image': 'url(add.png )',
				'background-color':'grey'
			});
			
			/* reset value of image id */
			$(this).prev().val('');
			
		});		
		
		
	}
}(jQuery));

