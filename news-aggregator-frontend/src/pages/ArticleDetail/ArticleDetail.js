import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import apiRequest from "../../lib/api";
import "./articleDetail.scss";
import Loader from "../../components/Loader/Loader";

const ArticleDetail = () => {
  const { id } = useParams(); // Extract the article ID from the URL
  const [article, setArticle] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const token = JSON.parse(localStorage.getItem("access_token"));

  useEffect(() => {
    const fetchArticleDetail = async () => {
      try {
        const res = await apiRequest.get(`/articles/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        setArticle(res.data.article);
      } catch (err) {
        console.error("Error fetching article:", err);
      } finally {
        setIsLoading(false);
      }
    };

    fetchArticleDetail();
  }, [id]);

  if (isLoading) {
    return <div className="loading"><Loader/></div>;
  }

  if (!article) {
    return <div>Article not found.</div>;
  }

  return (
     <div className="article-detail">
      <h1>{article.title}</h1>
      
      {article.url_to_image && (
        <img src={article.url_to_image} alt={article.title} className="article-image-below-title" />
      )}

      <p><strong>Published at:</strong> {article.published_at}</p>
      <p><strong>Source:</strong> {article.source.name}</p>
      
      <div className="article-content">
        <p>{article.content}</p>
      </div>

      <div className="article-categories">
        <h4>Categories:</h4>
        <ul>
          {article.categories.map((category) => (
            <li key={category.id}>{category.name}</li>
          ))}
        </ul>
      </div>

      <a href={article.url} target="_blank" rel="noopener noreferrer">
        <button className="external-link">Read Full Article</button>
      </a>
    </div>
  );
};

export default ArticleDetail;
