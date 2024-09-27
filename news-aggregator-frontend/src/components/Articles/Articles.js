import "./articles.scss";
import { Link } from "react-router-dom";
import { truncateText } from "../../utils/helperFunctions.js";

const ArticalCards = ({ articles }) => {
  if (!articles || articles.length === 0) {
    return (
      <div className="no-articles">
        <h4>No articles found.</h4>
        <p>Try adjusting your search criteria or check back later.</p>
        <img src="/no_result.svg" alt="No articles" />
      </div>
    );
  }

  return (
    <div className="category">
    {articles.map((article, index) => (
      <div className="card" key={index}>
        <img src={article.url_to_image || "/no-image.jpg"} alt={article.title} />
        <h4>{truncateText(article.title, 40)}</h4>
        <p dangerouslySetInnerHTML={{ __html: truncateText(article.description, 50) }}></p>
        <Link to={`/article/${article.id}`}>
          <button className="see-more">See More</button>
        </Link>
      </div>
    ))}
  </div>
  );
};

export default ArticalCards;