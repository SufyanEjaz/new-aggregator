import React, { useEffect, useState } from 'react'
import { Select, message } from "antd";
import { useNavigate } from "react-router-dom";
import "./preferance.scss"
import apiRequest from '../../lib/api';

function Preferences() {
    const [categories, setCategories] = useState([]);
    const [sources, setSources] = useState([]);
    const [authors, setAuthors] = useState([]);
    const [selectedCategories, setSelectedCategories] = useState([]);
    const [selectedSources, setSelectedSources] = useState([]);
    const [selectedAuthors, setSelectedAuthors] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    // const [error, setError] = useState("");
    const [userHasPreferences, setUserHasPreferences] = useState(false);
    const token = JSON.parse(localStorage.getItem("access_token"));
    const navigate = useNavigate();



    useEffect(() => {
        const fetchPreferences = async () => {
            setIsLoading(true);
            try {

                const res = await apiRequest.get("/preferences/all", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                const { preferences } = res.data;

                const categoryOptions = preferences.categories.map((category) => ({
                    value: category.id,
                    label: category.name,
                }));

                const sourceOptions = preferences.sources.map((source) => ({
                    value: source.id,
                    label: source.name,
                }));

                const authorOptions = preferences.authors
                .filter((author) => author && author.trim() !== "")
                .map((author) => ({
                    value: author,
                    label: author,
                }));

                setCategories(categoryOptions);
                setSources(sourceOptions);
                setAuthors(authorOptions);
                setIsLoading(false);
            } catch (err) {
                console.error("Error fetching preferences:", err);
            } finally {
                setIsLoading(false);
            }
        };

        fetchPreferences();
    }, [token]);

    useEffect(() => {
        const fetchUserPreferences = async () => {
        try {

            // fetch user's selected preferences
            const userPreferencesRes = await apiRequest.get("/preferences", {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            const userPreferences = userPreferencesRes.data.preferences?.settings || {};
            const selectedCategoryNames = categories
                .filter((category) => userPreferences.category_ids?.includes(String(category.value)))
                .map((category) => category.value);

            const selectedSourceNames = sources
                .filter((source) => userPreferences.source_ids?.includes(String(source.value)))
                .map((source) => source.value);

            setSelectedCategories(selectedCategoryNames || []);
            setSelectedSources(selectedSourceNames || []);
            setSelectedAuthors(userPreferences.authors || []);

            const hasPreferences = (
                (userPreferences.category_ids && userPreferences.category_ids.length > 0) ||
                (userPreferences.source_ids && userPreferences.source_ids.length > 0) ||
                (userPreferences.authors && userPreferences.authors.length > 0)
            );
            setUserHasPreferences(hasPreferences);
        } catch (err) {
            console.error("Error fetching user preferences:", err);
        }
        };
        
        fetchUserPreferences();
    }, [token, categories, sources]);
          
            
   

    const handleCategoryChange = (value) => {
        setSelectedCategories(value);
    };

    const handleSourceChange = (value) => {
        setSelectedSources(value);
    };

    const handleAuthorChange = (value) => {
        setSelectedAuthors(value);
    };
   
    const handleSubmit = async () => {
        
        if (
            selectedCategories.length === 0 &&
            selectedSources.length === 0 &&
            selectedAuthors.length === 0
        ) {
            message.error("Please select at least one preference i.e (category, source, author)")
            return;
        }
    
        // setError("");
    
        const formData = new FormData();
    
        selectedCategories.forEach((category) => {
            formData.append('categories[]', category);
        });
    
        selectedSources.forEach((source) => {
            formData.append('sources[]', source);
        });
    
        selectedAuthors.forEach((author) => {
            formData.append('authors[]', author);
        });
        try {
            setIsLoading(true);
            const res = await apiRequest.post("/preferences", formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data", 
                },
            });
    
            if (res.status === 200) {
                message.success(res.data.message)
                navigate("/")
            } else {
                message.error("Failed to save preferences")
                // setError("Failed to save preferences");
            }
        } catch (error) {
            message.error("Failed to save preferences")
            // setError("An error occurred while saving preferences");
        } finally {
            setIsLoading(false);
        }
    };
    

    return (
        <div className='preferance'>
            <div className='container'>
                <div className='heading'>Preferences</div>
                <div className="selectors">
                    <div className="selector">
                        <p>Select Category</p>
                        <Select
                            mode="multiple"
                            style={{ width: "100%" }}
                            placeholder="Select Categories"
                            onChange={handleCategoryChange}
                            filterOption={(input, option) =>
                                option.label.toLowerCase().includes(input.toLowerCase())
                            }
                            options={categories}
                            loading={isLoading}
                            value={selectedCategories}
                        />
                    </div>
                    <div className="selector">
                        <p>Select Sources</p>
                        <Select
                            mode="multiple"
                            style={{ width: "100%" }}
                            placeholder="Select sources"
                            onChange={handleSourceChange}
                            filterOption={(input, option) =>
                                option.label.toLowerCase().includes(input.toLowerCase())
                            }
                            options={sources}
                            loading={isLoading}
                            value={selectedSources}
                        />
                    </div>
                    <div className="selector">
                        <p>Select Authors</p>
                        <Select
                            mode="multiple"
                            style={{ width: "100%" }}
                            placeholder="Select Authors"
                            onChange={handleAuthorChange}
                            filterOption={(input, option) =>
                                option.label.toLowerCase().includes(input.toLowerCase())
                            }
                            options={authors}
                            loading={isLoading}
                            value={selectedAuthors}
                        />
                    </div>
                </div>
                {/* {error && <p style={{ color: 'red' }}>{error}</p>} */}
                <div className='buttons'>
                    <a href='/' className='skipButton'> <button className='skip'>{userHasPreferences ? "Cancel" : "Skip"}</button> </a>
                    <button
                      type="primary"
                      onClick={handleSubmit}
                      style={{ marginTop: "20px" }}
                      loading={isLoading}
                    > {userHasPreferences ? "Update" : "Submit"}
                    </button>
                </div>
            </div>
        </div>
    )
}

export default Preferences;
