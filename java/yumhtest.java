import org.json.JSONObject;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;
import org.w3c.dom.Document;

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
        String re="";
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
    //javac -cp .:json-20131018.jar:jsoup-1.11.3.jar yumhtest.java
	public static void main(String args[]) {
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
        String json = "{\"2\":\"efg\",\"1\":\"abc\"}";
        JSONObject b = new JSONObject(json);
        System.out.println(b.get("3"));
	}
}
