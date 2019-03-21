import org.json.JSONArray;
import org.json.JSONObject;
import org.jsoup.Connection;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;
import org.w3c.dom.Document;

import java.util.Iterator;
import java.io.File;
import java.sql.Array;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Pattern;
import java.util.regex.Matcher;
public class yumhtest{
    public static String  getUrlList(String html,Map configs){
        File input;
        org.jsoup.nodes.Document doc;
        String re="";
        try {
            input = new File("test.html");
            doc = Jsoup.parse(input, "UTF-8", "http://www.oschina.net/");
            Elements contents = doc.select(configs.get("itmes_select").toString());
            for (Element content : contents) {
                String link = content.select(configs.get("item_link_select").toString()).first().attr(configs.get("item_link_attr").toString());
                String title = "";
                if(configs.get("item_title_attr").toString() != "html") {
                    title = content.select(configs.get("item_title_select").toString()).first().attr(configs.get("item_title_attr").toString());
                }else{
                    title = content.select(configs.get("item_title_select").toString()).first().html();
                }
                String image = content.select(configs.get("item_image_select").toString()).first().attr(configs.get("item_image_attr").toString());
                if(!configs.get("link_contain").toString().isEmpty()){
                    if(link.indexOf(configs.get("link_contain").toString())==-1){
                        continue;
                    }
                }
                if(!configs.get("link_not_contain").toString().isEmpty()){
                    if(link.indexOf(configs.get("link_not_contain").toString())!=-1){
                        continue;
                    }
                }
                if(link.indexOf("http://")==-1 && link.indexOf("https://")==-1){
                    link = configs.get("main_url").toString() + link;
                }
                System.out.println("link is:"+link);
                System.out.println("title is:"+title);
                System.out.println("image is:"+image);
                System.out.println("");
            }
        }
        catch(Exception e) {
            System.out.println("eroor");
        }
        return "";
    }
    public static String getConents(String html,Map configs)
    {
        File input;
        org.jsoup.nodes.Document doc;
        String re= "";
        try {
            input = new File("content.html");
            doc = Jsoup.parse(input, "UTF-8", "http://www.oschina.net/");
            String s = doc.select(configs.get("content_select").toString()).first().attr(configs.get("content_attr").toString());
            System.out.println(s);
        }catch(Exception e) {
            System.out.println("eroor");
        }
        return "";
    }
    public static String getRequestUrl(String url,Map config){
        if(config.get("redict")=="1")
        {
            Connection.Response response = Jsoup.connect(url).followRedirects(true).ignoreContentType(true).execute();
            System.out.println(response.url());
        }
    }
    public static Map[] getRequestVideoUrls(String url,Map config){
        Map map1 =new HashMap();
        map1.put("P360","http://www.o.com/1.mp4");
        Map map2 =new HashMap();
        map2.put("P360","http://www.o.com/1.mp4");
        Map mapArr[] = { map1, map2 };
        return mapArr;
    }
    public static Map[] getVideoUrl(String url,String string_config){
        Map[] arrayConfigs = initConfigs(string_config);
        for(int i=0;i<arrayConfigs.length;i++){
            if(i==arrayConfigs.length-1){
                return getRequestVideoUrls(url,arrayConfigs[i]);
            }else{
                url = getRequestUrl(url,arrayConfigs[i]);
            }
        }
        Map map4 =new HashMap();
        Map  Map[] = {map4 };
        return Map;
    }
    public static Map[] initConfigs(String string_config){
        Map map1 =new HashMap();
        Map map2 =new HashMap();
        Map map3 =new HashMap();
        Map map4 =new HashMap();
        Map mapArr[] = { map1, map2, map3, map4 };
        return mapArr;
    }
    //javac -cp .:json-20131018.jar:jsoup-1.11.3.jar yumhtest.java
	public static void main(String args[]) {
        String url = "http://www.baidu.com";
        String configs ="ccc&&afa&scc||bbb&&ccc&&aaa";
        Map[] videos = getVideoUrl(url,configs);
      /*  Map<String,String> configs = new HashMap();
        String main_url = "http://www.javdoe.com";
        configs.put("main_url",main_url);

        String itmes_select = ".col-md-3.text-center.main-item";
        configs.put("itmes_select",itmes_select);
        String item_image_select = ".placeholder.iswatched";
        configs.put("item_image_select",item_image_select);
        String item_title_select = ".placeholder.iswatched";
        configs.put("item_title_select",item_title_select);
        String item_link_select = ".main-thumb";
        configs.put("item_link_select",item_link_select);
        String item_image_attr = "data-src";
        configs.put("item_image_attr",item_image_attr);
        String item_title_attr = "alt";
        configs.put("item_title_attr",item_title_attr);
        String item_link_attr = "href";
        configs.put("item_link_attr",item_link_attr);
        String link_contain = "a";
        configs.put("link_contain",link_contain);
        String link_not_contain = "";
        configs.put("link_not_contain",link_not_contain);
        String html = "fhd";
      //  String src = getUrlList(html,configs);
    //<iframe id="avcms_player" class="embed-responsive-item" src="/v/1xlg1891lvp" frameborder="0" allowfullscreen></iframe>
        String content_select = "#avcms_player";
        configs.put("content_select",content_select);
        String content_attr = "src";
        configs.put("content_attr",content_attr);
        String src = getConents(html,configs);*/
         /*try {

            String s  = Jsoup.connect("https://embed.media/api/source/7yvwl8xyxoj").referrer("https://embed.media/v/7yvwl8xyxoj").data("r", "", "d", "embed.media").method(Connection.Method.POST).header("Accept", "application/json").ignoreContentType(true).execute().body();;
           org.jsoup.nodes.Document doc = res2.parse();
        String title = doc.body();
        /*System.out.println(s );
      // String json_str = "{\"success\":true,\"player\":{\"logo_file\":\"\\/userdata\\/198722\\/player\\/866.png?v=1546851002\",\"logo_position\":\"top-right\",\"logo_link\":\"https:\\/\\/www3.javfinder.is\\/?ref=player\",\"logo_margin\":20,\"css_background\":\"rgba(0, 0, 0, 0)\",\"css_text\":\"#ffffff\",\"css_menu\":\"#333333\",\"css_mntext\":\"#e38a5a\",\"css_caption\":\"#000000\",\"css_cttext\":\"#ffffff\",\"css_ctsize\":\"12\",\"css_ctopacity\":\"1\",\"css_icon\":\"rgba(255, 255, 255, 0.8)\",\"css_ichover\":\"#e38a5a\",\"css_tsprogress\":\"#e38a5a\",\"css_tsrail\":\"rgba(255, 255, 255, 0.3)\",\"css_button\":\"#565656\",\"css_bttext\":\"#ffffff\",\"opt_autostart\":false,\"opt_title\":false,\"opt_quality\":true,\"opt_caption\":true,\"opt_download\":false,\"opt_sharing\":false,\"opt_playrate\":true,\"opt_mute\":false,\"opt_loop\":false,\"opt_vr\":true,\"restrict_domain\":\"\",\"restrict_action\":\"DoNothing\",\"restrict_target\":\"\",\"ads_adult\":true,\"ads_pop\":true,\"ads_vast\":true,\"trackingId\":\"UA-129195790-1\",\"viewId\":\"184994685\",\"income\":{\"client\":\"vast\",\"schedule\":{\"vast-pre\":{\"tag\":\"https:\\/\\/syndication.exosrv.com\\/splash.php?idzone=3255430\",\"offset\":\"pre\",\"skipoffset\":5},\"vast-50%\":{\"tag\":\"https:\\/\\/tsyndicate.com\\/do2\\/b05c09e60a9b4b3fb55132dfce704a3c\\/vast?\",\"offset\":\"50%\",\"skipoffset\":5},\"vast-80%\":{\"tag\":\"https:\\/\\/ca.clcknads.pro\\/v2\\/a\\/prl\\/vst\\/33967\",\"offset\":\"80%\",\"skipoffset\":5}}},\"incomePop\":[],\"aspectratio\":\"16:9\",\"poster_file\":\"\\/thumbnail\\/2019-03-21\\/7yvwl8xyxoj.jpg\",\"powered_text\":\"Javfinder 2.0\",\"powered_url\":\"https:\\/\\/www3.javfinder.is\\/?ref=player\",\"opt_nodefault\":true,\"opt_cast\":{\"appid\":\"00000000\"},\"resume_text\":\"Welcome JavFinder! You left off at xx:xx:xx. Would you like to resume watching?\",\"resume_yes\":\"Yes, Please\",\"resume_no\":\"No, Thanks\",\"opt_forceposter\":false,\"ads_free\":false,\"opt_parameter\":true,\"adb_enable\":false,\"adb_offset\":\"0\",\"adb_text\":\"Please turn off adblockers in order to continue watching\",\"revenue\":\"https:\\/\\/s20dh7e9dh.com\\/83\\/3b\\/87\\/833b870434edc35fb743c2615f5cb480.js\",\"revenue_fallback\":\"https:\\/\\/keqi7dh3df.com\\/83\\/3b\\/87\\/833b870434edc35fb743c2615f5cb480.js\",\"logger\":\"https:\\/\\/logger.pw\"},\"data\":[{\"file\":\"https:\\/\\/fvs.io\\/redirector?token=QmdaQ2RQQU1vd2M1NFpENjRxQUkwZGw4WEhIMTNBUjU3MnR6ZHp1eCtFdXhLWGQrbFdnKzMzOERnbDZ0VzJ6TElMQjRoSWFWZFRTV3pNWEF3ZFpCb1lidGFJaUJBQTZPZkhGQmtNVTVFTVBPeDl2UTZjUytKM3pON2V1NDJGdW5QWUxocUQrUjM3cHBLS1VTVmVuSm91dlRxeWM9OkxXL0Z1SmwrNnVRVGpkelpHdTZWL3c9PQ\",\"label\":\"480p\",\"type\":\"mp4\"},{\"file\":\"https:\\/\\/fvs.io\\/redirector?token=Qldza25kcDY3T3QxUDZJbFYwYkNhN0s3S0RSQU9CQVVocnVMUjdvU2pwc212ZmpVTlNJRjZjdmVGeEZBeDhhWmFuU2ZYNWcvcjBJa2N0OTJFRlJhN3hsL0xmQXJZZDFoR2pkcmZRVnRoYlNTeEwxRkIwdnBJL2JFQ3lWWTJ1bFZGdDVCVUJCNEdUcFA2RTJDS1FiMW9pdDQ1RnM9OjZsWnNaMG9LbGcreHU1RkZ5SElhdFE9PQ\",\"label\":\"720p\",\"type\":\"mp4\"},{\"file\":\"https:\\/\\/fvs.io\\/redirector?token=dUNGM1JwclBNdFgvRXduMG1zT3Z0V1k1M3BPZW1QYkVmMGFKSGdYSkFocnJhRzdGc0MxM2hrZWFUVE4vSTdVdTRmT3NFRmpVU1hZUzhpVFM4MlNWL0hGY01PeVZaV2FzMHNqRVVXSmtQTGtJWGtTbGxhNGlXMGluK2lMUW9NTmR0dFNlOEpYNWJOQUcxbzlob3BxTFBtMkV4RWJPOlFFVlMwem0zNW12Z25ndU9xRXNHOVE9PQ\",\"label\":\"1080p\",\"type\":\"mp4\"}],\"captions\":[],\"is_vr\":false}";
        JSONObject json = new JSONObject(s);
        JSONArray results = json.getJSONArray("data");
         for (int i = 0; i < results.length();i++) {
                 JSONObject object = (JSONObject) results.get(i);
                 String file = object.getString("file");
                Connection.Response response = Jsoup.connect(file).followRedirects(true).ignoreContentType(true).execute();
                System.out.println(response.url());

           }
        }catch(Exception e) {
            System.out.println(e.toString());
        }*/
	}
}
