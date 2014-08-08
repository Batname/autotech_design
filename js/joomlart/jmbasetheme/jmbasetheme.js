// JavaScript Document

var active_profile = "default";


var skinurl = baseurl+"/skin/frontend/default/"+defaulttheme+"/wavethemes/jmbasetheme/profiles";
    skinurl = skinurl.replace("admin","").replace("index.php","");
function getName(el,groups){
	var matchs = '';
	groups.each(function(group){
	   if (matches = el.attr("name").match("groups\\["+group+"\\]\\[fields\\]"+"\\[([^\\]]*)\\]"+"\\[value\\]")) 
	   { 
	     matchs = matches[1];
		 return;
	   }					 
	})
 	return matchs;	
}

function rebuilddata(group){
	
	var els = serializeArray(group);
	var json = {};
	els.each(function(el){
			
			var name = getName(el, group);
			if( name!=''){
				value = el.val().toString().replace (/\n/g, '\\n').replace (/\t/g, '\\t').replace (/\r/g, '').replace (/&/g, 'amp;amp;amp;amp;');
				if(values =  value.match(/[^\\]+\.(png|jpg|gif|bmp|jpeg|PNG|JPG|GIF|BMP)/)){
					value = values[0];
				}
				if(el.hasClass("input-file") && value != "" && value.indexOf("default/") < 0){
				   if(typeof storeid != "undefined"){
				     value = "stores/" + storeid +"/"+ value;
				   }else{	
                     value = "default/" +  value;
                   }

				}
				json[name] = value;
			}
			
	}, this);
	return json;

}

function serializeArray(groups){
    var els = new Array();
	var allelements = jQuery("#config_edit_form")[0].elements;
	
	var k = 0;
	groups.each(function(group){
					 
	     for (i=0;i<allelements.length;i++) {
		    var el = jQuery(allelements[i]);
			
			if (el.attr("name") && ( el.attr("name").indexOf("groups["+group+"]") >= 0 ) && (el.attr("type") !== "hidden" || el.attr("id") == "deleteimages") ){                 
		    	     els[k] = el;
		    	     k++;
		    }				 
	    }					   
    });  						  
	return els;
}

function filldata(profile,groups){
    active_profile = profile;	
    var els = serializeArray(groups);
	if(els.length==0) return;
	
	if (profiles[profile] == undefined) return;
	cprofile = profiles[profile];
	dprofile = profiles["default"];
	
	els.each(function(el){
       				  
		var name = getName(el,groups);
		
		var ogrname = el.attr("name");
		
		var id = el.attr("id");
		var value = (cprofile[name] != undefined)?cprofile[name]:((dprofile && dprofile[name] != undefined)?dprofile[name]:'');
		
		if(el.attr("type") == "file"){
			     
				   if(jQuery("img#"+el.attr("id")+"_image").length){
					   jQuery("img#"+el.attr("id")+"_image").parent("a").remove();
				   } 
			     
			      if(value !== '' && value !== undefined){
				     
					  aimage =  jQuery('<a/>',{
						 "onclick":"imagePreview('"+id+"_image'); return false;",   
						 "href":skinurl+"/"+active_profile+"/"+"images"+"/"+value
					  });
				
					  image = jQuery('<img/>',{
						  "width":22,
						  "height":22,
						  "class":"small-image-preview v-middle",
						  "alt":value,
						  "title":value,
						  "id":el.attr("id")+"_image",
						  "src":skinurl+"/"+active_profile+"/"+"images"+"/"+value
					  });
				      aimage.append(image)
				      el.before(aimage);
				      if(!el.next("span.delete-image").length){
                         el.after('<span class="delete-image"><input type="checkbox" id="'+el.attr("id")+'_delete" class="checkbox" value="'+value+'" name="'+el.attr("name")+'[delete]" style="color: black;"><label for="wavethemes_jmbasetheme_jmbasethemeblue_blueaddtocart_delete"> Delete Image</label><input type="hidden" value="'+value+'" name="'+el.attr("name")+'[value]"></span>') 
				      }else{
                         el.next("span.delete-image").show();
                      } 
			  	  }else if(el.next("span.delete-image").length > 0){
	                  el.next("span.delete-image").hide();  
	              } 
                   
				  if(el.next("span.delete-image").length > 0) {
				       
					  spandeleteimg = el.next("span.delete-image");
					  el.remove();
					  spandeleteimg.before('<input type="file" class="input-file" value="'+value+'" name="'+ogrname+'" id="'+id+'">');
				  }
		}else{
		      el.val(value);
		      jQuery.fn.mColorPicker.setInputColor(el.attr("id"),value);
		}
	});
}

function createprofile(){
	profilename = prompt(lg_enter_profile_name,'');
	
	
 	data = {};
	if(profilename == ""){
		alert(lg_please_enter_profile_name,'');
		return createprofile();
    }else if(profilename !== null){
       
		profilename = jQuery.trim(profilename).replace(' ', '').toLowerCase();
		jQuery("#wavethemes_jmbasetheme_jmbasethemegeneral_profile option").each(function(){
			if(jQuery.trim(jQuery(this).val()).toLowerCase() == profilename){
			   alert(lg_profile_name_exist.replace('%s', profilename));
               return createprofile();
			 
			}																							 
		});
		if(profilename !== null ) {
	   		url = baseurl+"jmbasetheme/index/createProfile";
			data["profile"] = profilename;
			data["settings"] =  "{'iscore':false}";
			sbmitform(url,data);
	    }
      
	}

}

function cloneprofile(oldprofile){
	 
    profilename = prompt(lg_enter_profile_name,"copy-"+oldprofile);
   
 	var profilehandle = jQuery("#wavethemes_jmbasetheme_jmbasethemegeneral_profile");
	data = {};
	if(profilename == ""){
		alert(lg_please_enter_profile_name,"copy-"+oldprofile);
		return cloneprofile(oldprofile);
    }else if(profilename !== null){
      	profilename = jQuery.trim(profilename).replace(' ', '').toLowerCase();
     	jQuery("#wavethemes_jmbasetheme_jmbasethemegeneral_profile option").each(function(){
			if(jQuery.trim(jQuery(this).val()).toLowerCase() == profilename){
			   alert(lg_profile_name_exist.replace('%s', profilename));
			   return cloneprofile(oldprofile);
			}																							 
		});
		if(profilename !== null ) {
			url = baseurl+"jmbasetheme/index/cloneProfile";
			data["oldprofile"] = profilehandle.val();
			data["profile"] = profilename;
			data["settings"] =  rebuilddata(["jmbasethemebase","jmbasetheme"+oldprofile]);
			sbmitform(url,data);
	    }
		
	}
}

function restoreprofile(profilename){
    data = {};
    url = baseurl+"jmbasetheme/index/restoreProfile";
    data["profile"] = profilename;
	sbmitform(url,data);
}

function deleteprofile(profilename){
	data = {};
	url = baseurl+"jmbasetheme/index/deleteProfile";
	data["profile"] = profilename;
	sbmitform(url,data);
}

function saveprofile(){
    var profilehandle = jQuery("#wavethemes_jmbasetheme_jmbasethemegeneral_profile");
    profilename = profilehandle.val();
    profilename = jQuery.trim(profilename).replace(' ', '').toLowerCase();
    data = {};
    url = baseurl+"jmbasetheme/index/saveProfile";
    data["profile"] = profilename;
    if(typeof storecode != 'undefined'){
    	data["storecode"] = storecode;
    }
	data["settings"] =  rebuilddata(["jmbasethemebase","jmbasethemedevice","jmbasethememobile","jmbasetheme"+profilename]);
	sbmitform(url,data);

}
function addsectionconfig(oldprofile,profilename){

     if(cprofileconfig = jQuery("#wavethemes_jmbasetheme_jmbasetheme"+oldprofile+"-head")){
		systemconfig = jQuery("#wavethemes_jmbasetheme_jmbasetheme"+oldprofile+"-head").parents("div.section-config");
		sectionHtml = systemconfig.html();
		oldreplace = "jmbasetheme"+oldprofile
		newsectionHtml = str_replace(oldreplace,"jmbasetheme"+profilename,sectionHtml);
		newsectionHtml = newsectionHtml.replace("Fieldset.applyCollapse"," ");
		newsystemconfig = jQuery('<div/>',{
			"class": "section-config",
			"style":"display:block"
		})
		newsystemconfig.html(newsectionHtml);
		newsystemconfig.find("a#wavethemes_jmbasetheme_jmbasetheme"+profilename+"-head").html("JM Basetheme: settings for "+profilename+" profile");
		systemconfig.after(newsystemconfig);
		
	 }  
}
function sbmitform(url,data){
	
	var profilehandle = jQuery("#wavethemes_jmbasetheme_jmbasethemegeneral_profile"); 
	jQuery.ajax({
	  type: 'POST',
	  url: url,
	  data: data,
	  success: function(result){
		    if(result.error){
			   alert(result.error);
			   return false;
			} 
			if(result.successful){
			   alert(result.successful);
			}
			if(result.profile){
			   
			   profiles[result.profile] = result.settings;
			   if(result.type == "clone"){
				    profilehandle.append('<option value="'+result.profile+'" selected="selected">'+result.profile+'</option>');
				    var oldprofile = result.oldprofile;
					var profilename = result.profile;
					addsectionconfig(oldprofile,profilename);
					profilehandle.trigger("change");
			   }else if(result.type == "saveProfile"){
                    configForm.submit();  
					
			   }else if(result.type == "new"){ 
			   	 profilehandle.append('<option value="'+result.profile+'" selected="selected">'+result.profile+'</option>');
			   	 addsectionconfig("core",result.profile);
			     profilehandle.trigger("change");
			   }else if(result.type == "restore"){
                  profiles[result.profile] = result.settings;
                  profilehandle.trigger("change");
			   }else if(result.type == "delete"){
			   	  profilehandle.find("[value='"+result.profile+"']").remove();
			   	  profilehandle.val("default");
                  profilehandle.trigger("change");
			   }
			}
	  },
	  dataType: "json"
	});
}

jQuery(document).ready(function(){
   var profilehandle = jQuery("#wavethemes_jmbasetheme_jmbasethemegeneral_profile");
   //add the class for the title
   jQuery('tr[id*="row_wavethemes_jmbasetheme_jmbasethemebase_title"],tr[id*="row_wavethemes_jmbasetheme_jmbasethemedevice_title"],tr[id*="row_wavethemes_jmbasetheme_jmbasethememobile_title"]').addClass("row_wavethemes_jmbasetheme_wavethemes_jmbasetheme_title").each(function(index,item){
        jQuery(item).children('td[class="scope-label"]').html("");																																							   });
   jQuery("fieldset#wavethemes_jmbasetheme_jmbasethemegeneral").css({"display":"block"});
   jQuery("button.save").attr("onclick","").live("click",function(e){
   	    jQuery('input:file[value=""]').attr('disabled', true);
        e.preventDefault();
        saveprofile();
   })

   //Delete image checkbox
   jQuery("span.delete-image input[type='checkbox']").live("click",function(e){
   	    if(!jQuery("#deleteimages").length){
   	       jQuery(this).parent().after("<input type='hidden' name='groups[jmbasethemebase][fields][deleteimages][value]' id='deleteimages' value='' />");	
   	    }
        if(jQuery(this).attr("checked")){
          if(jQuery("#deleteimages").val() == ""){
          	jQuery("#deleteimages").val(jQuery(this).val()+",");
          }else{
          	jQuery("#deleteimages").val(jQuery("#deleteimages").val()+jQuery(this).val()+",");
          } 	
          jQuery(this).val("");  
        }else{
           jQuery("#deleteimages").val(jQuery("#deleteimages").val().replace(jQuery(this).val()+",","")); 
           jQuery(this).val(jQuery(this).siblings("input[type='hidden']").val());    
        }


   })
  // jQuery("span.delete-image").css({"display":"none"});   
   jQuery(".input-file").live("change",function(){
   	    
        if(jQuery("#"+jQuery(this).attr("id")+"_delete").length){
        	
              if(jQuery(this).val() !== "" ){
              	jQuery("#"+jQuery(this).attr("id")+"_delete").attr("name","delete_"+jQuery(this).attr("id"));
              }else {
              	jQuery("#"+jQuery(this).attr("id")+"_delete").attr("name",jQuery(this).attr("name")+"[delete]");
              }

        }

   });
   
   //change the active profile
   profilehandle.bind("change",function(){
	    var nextprofile = jQuery(this).val();
	    jQuery("span.delete-image input[type='checkbox']").attr("checked",false);
	    if(!jQuery("#deleteimages").length){
	    	jQuery("#deleteimages").val("");
	    }
	   
		jQuery(".section-config").each(function(index,config){
		   	fieldset = jQuery(config).children("fieldset");
			if(fieldset.attr("id").indexOf("jmbasetheme"+nextprofile) < 0 && fieldset.attr("id") !=="wavethemes_jmbasetheme_jmbasethemegeneral" && fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethemebase" && fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethemedevice" && fieldset.attr("id") !== "wavethemes_jmbasetheme_jmbasethememobile"){
			  jQuery(config).hide(); 
			}else{
			  jQuery(config).show();   
			}
		});
		
		filldata(jQuery(this).val(),["jmbasethemebase","jmbasethemedevice","jmbasethememobile","jmbasetheme"+nextprofile]);
		
		 //hide the restore button if this's not a core profile
        if(profiles[nextprofile]["iscore"]){
        	//jQuery("span.delete-image").css({"display":"none"});
        	jQuery("button#restore-profile").css({"display":"block"});
        	jQuery("button#delete-profile").css({"display":"none"});
        }else{
        	//jQuery("span.delete-image").css({"display":"block"});
        	jQuery("button#delete-profile").css({"display":"block"});
        	jQuery("button#restore-profile").css({"display":"none"});
        }

   });
   profilehandle.trigger("change");
   
   //create new profile
   jQuery("#create-profile").bind("click",function(){
	   createprofile(); 											   
   });
   
   //clone the active profile
   jQuery("#clone-profile").bind("click",function(){
	   cloneprofile(profilehandle.val()); 											   
   });
   
   //restore core profiles to default values
   jQuery("#restore-profile").bind("click",function(){
	   restoreprofile(profilehandle.val()); 											   
   });

   //delete non-core profiles 
   jQuery("#delete-profile").bind("click",function(){
	   deleteprofile(profilehandle.val()); 											   
   });
    
   
})



function str_replace (search, replace, subject, count) {
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Gabriel Paderni
  // +   improved by: Philip Peterson
  // +   improved by: Simon Willison (http://simonwillison.net)
  // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   bugfixed by: Anton Ongson
  // +      input by: Onno Marsman
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +    tweaked by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   input by: Oleg Eremeev
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Oleg Eremeev
  // %          note 1: The count parameter must be passed as a string in order
  // %          note 1:  to find a global variable in which the result will be given
  // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
  // *     returns 1: 'Kevin.van.Zonneveld'
  // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
  // *     returns 2: 'hemmo, mars'
  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp).split(f[j]).join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}