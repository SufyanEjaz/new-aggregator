import { useEffect } from 'react';

const useNavbarScrollEffect = () => {
    useEffect(() => {
      const navbar = document.querySelector('.navbar');
      const handleScroll = () => {
        if (window.scrollY > 150) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      };
  
      window.addEventListener('scroll', handleScroll);
  
      return () => {
        window.removeEventListener('scroll', handleScroll);
      };
    }, []);
  };
  

export default useNavbarScrollEffect;
