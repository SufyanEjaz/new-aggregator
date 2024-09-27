import { useEffect, useState } from "react";
import SearchBar from "../../components/SearchBar/SearchBar";
import apiRequest from "../../lib/api";
import "./homePage.scss";
import Loader from "../../components/Loader/Loader";
import ArticalCards from "../../components/Articles/Articles";

const getPageNumber = (pageUrl) => {
  if (!pageUrl) return null;
  const urlParams = new URLSearchParams(pageUrl.split('?')[1]);
  return urlParams.get('page');
};

const HomePage = () => {
  const [articles, setArticles] = useState([]);
  const [pagination, setPagination] = useState({});
  const [searchParams, setSearchParams] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const token = JSON.parse(localStorage.getItem("access_token"));

  const fetchArticles = async (url = "/articles", params = "") => {
    setIsLoading(true);
    try {
      const res = await apiRequest.get(`${url}${params}`, {
        headers: {
          Authorization: `Bearer ${token}`
        },
      });

      const articlesData = res.data.articles;
      setArticles(articlesData.data);
      setIsLoading(false);

      setPagination({
        current_page: articlesData.current_page,
        next: articlesData.next_page_url ? getPageNumber(articlesData.next_page_url) : null,
        prev: articlesData.prev_page_url ? getPageNumber(articlesData.prev_page_url) : null,
      });
    } catch (err) {
      console.error("Error fetching articles:", err);
    } finally {
      setIsLoading(false);
    }
  };


  useEffect(() => {
    fetchArticles();
  }, []);


  const handleSearchResults = (fetchedArticles, searchParamsString) => {
    setArticles(fetchedArticles.data);
    setSearchParams(searchParamsString);

    setPagination({
      current_page: fetchedArticles.current_page,
      next: fetchedArticles.next_page_url ? getPageNumber(fetchedArticles.next_page_url) : null,
      prev: fetchedArticles.prev_page_url ? getPageNumber(fetchedArticles.prev_page_url) : null,
    });
  };

  const handlePageChange = (page) => {
    if (page) {
      const params = new URLSearchParams(searchParams);
      params.set('page', page);
      fetchArticles("/articles", `?${params.toString()}`);
    }
  };

  return (
    <>
      <div className="home">
        <div className="heading">
          <h3>
            {"Article Search!"}
          </h3>
          <div class="divider div-transparent div-dot"></div>
        </div>
        <SearchBar onSearch={handleSearchResults} />
        <div className="Cards">
          {isLoading ? (<>
            <div className="spinner">
              <Loader />
            </div>
          </>) : (<>
            <ArticalCards articles={articles} />
          </>)}

        </div>

        <div className="pagination">
          <button
            onClick={() => handlePageChange(pagination.prev)}
            disabled={!pagination.prev || isLoading}
            className="prev"
          >
            <span>{"<<"}</span> {"Previous"}
          </button>
          <span>Page { pagination.current_page }</span>
          <button
            onClick={() => handlePageChange(pagination.next)}
            className="next"
            disabled={!pagination.next || articles.length < 15 || isLoading}
          >
            {"Next"} <span>{">>"}</span>
          </button>
        </div>

      </div>
    </>
  );
};

export default HomePage;