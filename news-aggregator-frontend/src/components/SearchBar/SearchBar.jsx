import "./searchBar.scss";
import { Select } from "antd";
import apiRequest from "../../lib/api";
import { useEffect, useState } from "react";

const SearchBar = ({ onSearch }) => {
  const [preferences, setPreferences] = useState({
    categories: [],
    sources: [],
  });
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [selectedSources, setSelectedSources] = useState([]);
  const [date, setDate] = useState("");
  const [keyword, setKeyword] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const token = JSON.parse(localStorage.getItem("access_token"));


  useEffect(() => {
    const fetchPreferences = async () => {
      setIsLoading(true);
      try {

        const { data } = await apiRequest.get("/preferences/all", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        setPreferences({
          categories: data.preferences.categories.map(({ id, name }) => ({
            value: id,
            label: name,
          })),
          sources: data.preferences.sources.map(({ id, name }) => ({
            value: id,
            label: name,
          })),
        });
        setIsLoading(false);
      } catch (err) {
        console.error("Error fetching preferences:", err);
      } finally {
        setIsLoading(false);
      }
    };

    fetchPreferences();
  }, [token]);

  const handleSearch = async () => {
    try {
      const params = new URLSearchParams({
        category: selectedCategories.join(","),
        source: selectedSources.join(","),
        date,
        keyword
      }).toString();

      const { data } = await apiRequest.get(`/articles?${params}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      onSearch(data.articles, `&${params}`);
    } catch (err) {
      console.error("Error fetching articles:", err);
    }
  };

  const handleDateChange = (e) => {
    setDate(e.target.value);
  };

  const handleKeywordChange = (e) => {
    setKeyword(e.target.value);
  };


  return (
    <div className="searchbar">
      <div className="searchContainer">

        <div className="mainSearch">
          <div className="search">
            <div className="inputbox">
              <input
                type="search"
                placeholder="Search here..."
                value={keyword}
                onChange={handleKeywordChange}
              />
            </div>
          </div>
        </div>

        <div className="selectors">
        <div className="selector">
          <p>Select Category</p>
          <Select
            mode="multiple"
            style={{ width: "100%" }}
            placeholder="Select Categories"
            onChange={setSelectedCategories}
            filterOption={(input, option) =>
              option.label.toLowerCase().includes(input.toLowerCase())
            }
            options={preferences.categories}
            loading={isLoading}
          />
        </div>
        <div className="selector">
          <p>Select Sources</p>
          <Select
            mode="multiple"
            style={{ width: "100%" }}
            placeholder="Select sources"
            onChange={setSelectedSources}
            filterOption={(input, option) =>
              option.label.toLowerCase().includes(input.toLowerCase())
            } 
            options={preferences.sources}
            loading={isLoading}
          />
        </div>
        <div className="selector">
          <p>Select Date</p>
          <input type="date" value={date} onChange={handleDateChange} />
        </div>
      </div>
        <div className="buttons">
          <button className="button" onClick={handleSearch}>Search</button>
        </div>
      </div>
    </div>
  );
};

export default SearchBar;